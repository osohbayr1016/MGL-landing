<?php
class newsTypeClass
{
	function is_newsType($table,$newsID,$newsType_id,$table1)
	{
		$where	= "`table`='$table1' and news_id=$newsID and type_id=".$newsType_id;
		$sql	= "SELECT news_id FROM $table WHERE $where ";
		$query	= mysql_query($sql);
		if(mysql_num_rows($query)>0) return 1;
		else return 0;
	}
	function sel_newsType($table,$newsID,$table1)
	{
		$where	= "`table`='$table1' and news_id=$newsID ";
		$sql	= "SELECT type_id FROM $table WHERE $where order by type_id ";
		$query	= mysql_query($sql);
		while($object = mysql_fetch_object($query)){
			
			$print_type[]=array(
					"type_id"			=>$object->type_id,
					);
				}
		return $print_type[0]["type_id"];
	}
	function add_newsType($table,$news_id,$newsType_id,$table1)
	{
		$sql	= "INSERT $table VALUES('$news_id','$newsType_id','$table1')";
		$query	= mysql_query($sql);
	}
	function del_newsType($table,$news_id,$table1)
	{
		$sql	= "DELETE FROM $table WHERE (news_id = $news_id or news_id = ($news_id*-1) ) and `table`='$table1'";
		$query	= mysql_query($sql);
	}	
}
?>