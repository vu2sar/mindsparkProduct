<?php

/**
* 
* @param $sqlText				This variable contains the sql query itself
* @param $schoolCode			This variable contains the schoolCode
* @param $tableName				This variable contains the table name from which data is to be deleted
* @param $whereClauseFields		This will have all the where condition parameters as key value pair
* @param $whereCondPart1		This will have the query part which contains the autoincrement field, e.g. srno IN (6,8,9)
* @param $whereCondPart2		This will have the rest of where condition part, after removing the part passed in above variable
* 
*/
function logDeleteQuery($sqlText, $tableName, $schoolCode, $whereClauseFields=array(), $whereCondPart1='', $whereCondPart2='',$mode=0)
{
	$modifiedSqlText = '';
	//If where condition does not have any variables, it does not modify query
	if($mode==1)
	{
		$modifiedSqlText = $sqlText;
	}
	else if(count($whereClauseFields) > 0)
	{
		//Gets the auto increment field name from database.
		$sql = "SELECT autoIncrementFieldName FROM adepts_syncTableDetail WHERE tableName='$tableName' AND autoIncrementFieldName!=''";
		//Checks them against the passed variables in whereClauseFields, 
		//i.e if table does not contain autoincrement in where condition, query will not be modified..
		//If condition below will not be satisfied and execute the else condition..
		$fieldArr = array_keys($whereClauseFields);
		$sql .= " AND FIND_IN_SET(autoIncrementFieldName, '".implode(",",$fieldArr)."')";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_assoc($result);
			$autoIncrementFieldName = $row['autoIncrementFieldName'];
			
			//Replaces the auto increment variable with the blank to get the condition operator and values
			//i.e. from "srno IN (18,19,20)" it will extract  the "IN (18,19,20)" part
			$whereCondPart1 = str_replace($autoIncrementFieldName,'',$whereCondPart1);
			$whereCondPart1 = preg_replace('/\s*,\s*/', ',', $whereCondPart1);
			$whereClauseFields[$autoIncrementFieldName] = preg_replace('/\s*,\s*/', ',', $whereClauseFields[$autoIncrementFieldName]);
			
			//Gets the auto increment ids of other server wrt to passed auto increments..
			$referenceValues = '';
			
			//Replaces the auto increment values by other server's auto increment valus found in mapping table..
			$whereCondPart3 = $whereCondPart1;
			$sql = "SELECT referenceAutoIncrement FROM adepts_autoIncrementMapping WHERE tableName='$tableName' AND autoIncrementVal IN (".$whereClauseFields[$autoIncrementFieldName].")";
			$result = mysql_query($sql);
			while($row = mysql_fetch_assoc($result))
			{
				$whereCondPart3 = str_replace($row['referenceAutoIncrement'],'',$whereCondPart3);
				$whereCondPart3 = str_replace(',,',',',$whereCondPart3);
			}
			
			//Removes trailing commas..
			$whereCondPart3 = trim($whereCondPart3,',');
			
			//Removes the blank conditions, may appear in becuase of replacements of the auto increment values..
			$whereCondPart3 = preg_replace('/\s*IN\s*\(\s*\)\s*/'," IN ('')",$whereCondPart3);
			$whereCondPart3 = preg_replace("/\s*=\s*'*\s*'*\s*/"," = '' ",$whereCondPart3);
			
			//Concating all the parts..
			$modifiedSqlText = "DELETE A FROM $tableName A INNER JOIN adepts_autoIncrementMapping B ON A.$autoIncrementFieldName=B.autoIncrementVal AND B.syncSchoolCode=$schoolCode WHERE B.tableName='$tableName' AND (B.referenceAutoIncrement $whereCondPart1 OR A.$autoIncrementFieldName $whereCondPart3) AND $whereCondPart2";
		}
		else
		{
			$modifiedSqlText = $sqlText;
		}
	}
	else
	{
		$modifiedSqlText = $sqlText;
	}
	$sqInsert = "INSERT INTO adepts_databaseChangesQueries SET sqlQuery='".mysql_real_escape_string($modifiedSqlText)."'";
	mysql_query($sqInsert);
}
?>