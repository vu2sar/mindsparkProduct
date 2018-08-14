<?php 
/**
* @desc Wrapper class for session operations
* @since 24-june-09
*/
class CI_User_Session { 
	/**
    * func - __construct() 
    * @Access - public 
    * @Desc - The cunstructor used for warming up and preparing the sessions. 
    */ 
    function __construct() 
    { 
        session_start(); 
    } 
	
    /**
    * func - set_var() 
    * @Access - public 
    * @Desc - Set a session variable 
    * @param $var_name - the variable name 
    * @param $var_val  - value for $$var_name 
    */ 
    function set_var( $var_name, $var_val ) 
    { 
        if( !$var_name || !$var_val ) 
        { 
            return false; 
        } 
        $_SESSION[$var_name] = $var_val; 
    } 

	/**
    * func - set_vars() 
    * @Access - public 
    * @Desc - Delete session variables contained in an array 
    * @param $arr -  Array of the elements to be deleted 
    */ 
    function set_vars( $arr ) 
    { 
        if( !is_array( $arr ) ) 
        { 
            return false; 
        } 
        foreach( $arr as $key=>$val ) 
        { 
            //if(isset($_SESSION[$element]))
			//unset( $_SESSION[$element] ); 
			if( !$key || !$val ) 
			{ 
				continue;
			} 
			else
			{
				$_SESSION[$key] = $val;
			}
        } 
        return true; 
    }

    /**
    * func - get_var() 
    * @Access - public 
    * @Desc - Get a session variable 
    * @param $var_name -  the variable name to be retrieved 
    */ 
    function get_var( $var_name ) 
    { 
		if(isset($_SESSION[$var_name]))
			return $_SESSION[$var_name];
		else
			return '';
    } 

    /**
    * func - delete_var() 
    * @Access - public 
    * @Desc - Delete a session variable 
    * @param $var_name -  the variable name to be deleted 
    */ 
    function del_var( $var_name ) 
    { 
        unset( $_SESSION[$var_name] ); 
    } 

    /**
    * func - delete_vars() 
    * @Access - public 
    * @Desc - Delete session variables contained in an array 
    * @param $arr -  Array of the elements to be deleted 
    */ 
    function del_vars( $arr ) 
    { 
		if( !is_array( $arr ) ) 
        { 
			return false; 
        } 
        foreach( $arr as $key=>$element) 
        { 
            unset($_SESSION[$key]); 
        } 
        return true; 
    } 

    /**
    * func - delete_all_vars() 
    * @Access - public 
    * @Desc - Delete all session variables 
    * @param - None 
    */ 
    function del_all_vars() 
    { 
        $_SESSION = array();
    } 

    /**
    * func - end_session() 
    * @Access - public 
    * @Desc - Des! ! troy the session 
    * @param - None 
    */ 
    function end_session() 
    { 
        $_SESSION = array(); 
        session_destroy(); 
    } 

	
	/**
	* @desc Regenerate session id
	* @since 24-june-09
	*/
	function regenerateId($del_old_data = false)
	{
		return session_regenerate_id($del_old_data);
	}
	
	/**
	* @desc returns either a new session id or already propogated session id
	*/
	function getSessionId()
	{
		return isset($_GET['PHPSESSID'])?$_GET['PHPSESSID']:session_id();
	}
}// End class sessions 
?>