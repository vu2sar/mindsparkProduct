<?php

//echo "called";
function get_limited_query($sql_query,$from,$length)
{
	
	$sql_query=strtolower($sql_query);
	if (strpos($sql_query,'limit')>-1) {
		
		$sql_query=substr_replace($sql_query,'limit',strpos($sql_query,'limit')-strlen('limit'),strlen($sql_query));
	}

	$sql_query.=" LIMIT $from,$length";
	//echo $sql_query;
	return $sql_query;
}


function get_count_sql_query($sql_query)
{
	//SELECT *(,?([^,]*),?)* *FROM.*
	//select * from
}

function get_total_rows_number_via_sql_query($sql_query)
{
	//global $record_count;
//	$sql_query=get_count_sql_query($sql_query);
//	return custom_get_value_from_database($sql_query,'count(*)');
	$sql_query_result=mysql_query($sql_query);
	//$record_count=mysql_num_rows($result);
	return mysql_num_rows($sql_query_result);
}

/*
Sample :
	$paging=new CPaging(null,40,$sql_query);
	$paging->link='http://test.com';
	$sql_query=$paging->get_limited_query();
*/
class CPaging
{
	var $qsn_from='from';
	var $qsn_to='to';
	var $next_word='Next';
	var $previous_word='Previous';
	var $total;
	var $from=0;
	var $to;
	var $limit=40;
	var $link;
	var $begin_value=1;
	var $sql_query;
	var $auto_recognize=true;
	
	function CPaging($total=null,$limit=40,$sql_query=null)
	{
		if (!is_null($sql_query)) {$total=get_total_rows_number_via_sql_query($sql_query);}
		$this->total=$total;
		$this->limit=$limit;
		$this->sql_query=$sql_query;
	}
	
	
	function get_limited_query($sql_query=null)
	{
		$this->set_variables();
		if (is_null($sql_query)) {$sql_query=$this->sql_query;}
		$from=$this->from-$this->begin_value;
		return get_limited_query($sql_query,$from,abs($this->to-$from));
	}
	
	
	function set_variables()
	{
		if ($this->auto_recognize) {
			$this->from=$_GET[$this->qsn_from];
			$this->to=$_GET[$this->qsn_to];
		}
		if ($this->from<$this->begin_value) {$this->from=$this->begin_value;}
		if ($this->to<$this->begin_value) {$this->to=$this->limit;}
		if ($this->to>$this->total) {$this->to=$this->total+$this->begin_value;}
	}
	
	function show()
	{
		$from=$this->from;
		$to=$this->to;
		$total=$this->total;//+$this->begin_value;

		if ($total > $this->limit)
		{
//			$total=$this->total+$this->begin_value;
			$prev_hyper_link='';
			$next_hyper_link='';
			$this->set_variables();
			
			if ($this->from > $this->begin_value)
			{
				$from=$this->from-$this->limit;
				if ($from<$this->begin_value) {$from=$this->begin_value;}
				$to=$from+$this->limit-1;
				if ($to>$total) {$to=$total;}
	    		$prev_hyper_link = '<a href="'.$this->get_url($from,$to).'">&lt;'.$this->previous_word.'</a>';
			}
			if ($this->to < $total)
			{
				$to=$this->to+$this->limit;
				if ($to>$total) {$to=$total;}
				$from=$to-$this->limit+1;
				if ($from<$this->begin_value) {$from=$this->begin_value;}
				if ($from<=$this->to) {$from=$this->from+$this->limit;}
	    		$next_hyper_link = '<a href="'.$this->get_url($from,$to).'">'.$this->next_word.'&gt;</a>';
			}
			$result=$prev_hyper_link.'&nbsp;('.$this->from.'-'.$this->to.')/('.$total.')&nbsp;'.$next_hyper_link;
		}
		//echo "result is ".$result;
		return $result;
	}
	
	
	
	function get_url($from=null,$to=null,$link=null)
	{
		if (is_null($from)) {$from=$this->from;}
		if (is_null($to)) {$to=$this->to;}
		if (is_null($link)) {$link=$this->link;}
	
		$result=$link;	
		if (strpos($link,'?')==false) {$result.='?';} else {$result.='&';}
		$result.="$this->qsn_from=$from&$this->qsn_to=$to";
		return $result;
	}
	
}
?>