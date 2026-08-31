<?php
class adminClass
{
	function select_allAdmin($table,$where)
	{
		
		$sql	= "SELECT * FROM $table	WHERE $where ORDER BY `id` ASC  LIMIT 0,100";
		$query	= mysql_query($sql);		
		while($object = mysql_fetch_object($query)){
			
			$print_user[]=array(
					"id"=>$object->id,
					"name"=>$object->name,
					"mail"=>$object->mail,
					"aname"=>$object->aname,
					"apass"=>$object->apass,					
					"acount"=>$object->acount,
					"action"=>$object->action,					
					"active"=>$object->active
					);
		}
		return $print_user;
	}
	function select_Admin($table,$table1,$where)
	{
		
		$sql	= "SELECT menu_id,admin_id FROM $table,$table1	WHERE $where";
		$query	= mysql_query($sql);		
		while($object = mysql_fetch_object($query)){
			
			$print_user[]=array(
					"admin_id"=>$object->admin_id,
					"menu_id"=>$object->menu_id
					);
		}
		return $print_user;
	}
	
	function select_oneAdmin($table,$where)
	{
		$sql	= "SELECT * FROM $table	WHERE $where ORDER BY `id` ASC  LIMIT 0,1";
		$query	= mysql_query($sql);		
		while($object = mysql_fetch_object($query)){
			
			$print_user[]=array(
					"id"=>$object->id,
					"name"=>$object->name,
					"mail"=>$object->mail,
					"aname"=>$object->aname,
					"apass"=>$object->apass,					
					"acount"=>$object->acount,
					"action"=>$object->action,					
					"active"=>$object->active
					);
		}
		return $print_user;
	}
	function select_admin_name($table,$id)
	{
		$where=" id=".$id;
		$sql	= "SELECT name FROM $table	WHERE $where ";
		$query	= mysql_query($sql);
		$object = mysql_fetch_object($query);
		return $object->name;
	}
	function addAdmin($table,$post_value)
	{
		
		$post_name			= $post_value['post_name'];
		$userID				= $post_value['userID'];
		$post_mail			= $post_value['post_mail'];
		$post_aname			= $post_value['post_aname'];
		$post_apass			= $post_value['post_apass'];	
		$post_acount		= $post_value['post_acount'];
		$post_action		= $post_value['post_action'];
		$post_active 		= $post_value['post_active'];
		
		$sql	= "INSERT INTO `$table`( 
									`id` , 
									`userID` ,
									`name` , 
									`mail` , 
									`aname` , 
									`apass` ,
									`acount` , 
									`action` , 
									`active`
									 )
							VALUES(
									'',
									'$userID',
									'$post_name',
									'$post_mail',
									'$post_aname',
									'$post_apass',
									'$post_acount',
									'$post_action',
									'$post_active'						
									)";
		$query	= mysql_query($sql);
		if($query) return 1;
		else return 0;
	}
function editAdmin($table,$value,$id){
	
		$post_name			= $value['post_name'];
		$post_mail			= $value['post_mail'];
		$post_aname			= $value['post_aname'];
		$post_apass			= $value['post_apass'];	
		$post_acount		= $value['post_acount'];
		$post_action		= $value['post_action'];
		$post_active 		= $value['post_active'];
		
		$sql	= "UPDATE $table 
					SET 
						`name`			= '$post_name',
						`mail`			= '$post_mail',
						`name`			= '$post_name',
						`aname`			= '$post_aname',
						`apass`			= '$post_apass',
						`acount`		= '$post_acount',
						`action`		= '$post_action',
						`active`		= '$post_active'
					WHERE id=$id";
					
		$query	= mysql_query($sql);
	}	
function delAdmin($table,$id)
	{		
		$sql	= "DELETE FROM $table WHERE id = $id";
		$query	= mysql_query($sql);
	}
function is_menu_admin($table,$where){
		$sql	= "SELECT menu_id FROM $table WHERE $where ";
		$query	= mysql_query($sql);
		if(mysql_num_rows($query)>0) return 1;
		else return 0;
	}
function is_menu_active_admin($table,$admin_id,$menu_id){
		$where  = " admin_id=$admin_id and menu_id=$menu_id";
		$sql	= "SELECT menu_id FROM $table WHERE $where ";
		$query	= mysql_query($sql);
		if(mysql_num_rows($query)>0) return 1;
		else return 0;
	}
function sendActive($random,$our_mail,$send_mail){
		
	}
function activeAdmin($table,$menu_id,$admin_id)
	{
		$sql	= "INSERT $table VALUES('$admin_id','$menu_id')";
		$query	= mysql_query($sql);	
	}
function deActiveAdmin($table,$where)
	{
		$sql	= "DELETE FROM $table WHERE $where";
		$query	= mysql_query($sql);
	}
function isEmail($table, $mail){
		$where = "email='$mail'";
		$sql	= "SELECT * FROM $table	WHERE $where ";
		$query	= mysql_query($sql);	
		mysql_num_rows($query);
		if(mysql_num_rows($query)==0) return 1;
		else return 0;
	}
function emailOK($str) {
	//Check empty
	if(empty($str)) return false;

	//Check for @
	if(!ereg("@",$str)) return false;
	
	//Check for at least 1 dot
	if(!ereg("\.",$str)) return false;

	//Get a user and a host
	list($user, $host) = explode("@", $str);
	
	//Make sure we have a user and host
	if((empty($user)) || (empty($host))) return false;
	
	//These characters are not allowed in email addresses
	$badChars = "[ ]+| |\+|=|[|]|{|}|`|\(|\)|,|;|:|!|<|>|%|\*|/|'|\"|~|\?|#|\\$|\\&|\\^|www[.]";
	return !eregi($badChars, $str);
}//End Function
}
?>