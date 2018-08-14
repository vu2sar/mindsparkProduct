<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
@ Amit Kumar Varshney To Check Params 

**/
if ( ! function_exists('checkids'))
{
    function checkids($table,$column_name,$id)
	{
	   
	   	$ci = & get_instance();
	   	$q=$ci->db->get_where($table,array($column_name => $id));
	   	if($q->num_rows() > 0)
	   	{
	   			return 1;
	   	}
	   	else
	   	{
	   			return 0;
	    }
	  }
	  
}
if ( ! function_exists('checkparams'))
{
    function checkparams($postdata,$params,$oparams='') 
	{ 	
		$cparams=($oparams!='')?count($oparams):0;
		$check=0;
		if ((count($postdata) <= count($params) + $cparams) && (count($postdata) >= count($params) )) 
	  	{
	  		$check=0;
		  	foreach ($postdata as $key=>$value) 
		  	{
		  		if (in_array($key,$params, TRUE)) 
		  		{
		  			$check=1;
		  			unset($params[array_search($key,$params)]);
		  		}
		  		else
		  		{
		  			if ($cparams != 0) 
		  			{
		  				if (in_array($key,$oparams, TRUE)) 
				  		{
				  			$check=1;
				  			unset($oparams[array_search($key,$oparams)]);
				  		}
				  		else
				  		{
				  			return 0;
				  		}
		  			}
		  			else
		  			{
		  				return 0;
		  			}
		  			
		  		}
		  	}
	  	}
	  	if($check)
	  	{	
	  		if (count($params) == 0) 
	  		{
	  			return 1;
	  		}
	  		else
	  		{
	  			return 0;
	  		}
	  	}
	  	else
	  	{
	  		return 0;
	  	}
	}
}
?>