<?php
class wallUpdates {
    
     // Updates   	
	  public function Updates($uid) 
	{
	    $query = mysql_query("SELECT M.msg_id, M.uid_fk, M.message, M.created, U.username FROM messages M, users U  WHERE M.uid_fk=U.uid and M.uid_fk='$uid' order by M.msg_id desc ") or die(mysql_error());
         while($row=mysql_fetch_array($query))
		$data[]=$row;
	    return $data;
		
    }
	//Comments
	   public function Comments($msg_id) 
	{
	    $query = mysql_query("SELECT C.com_id, C.uid_fk, C.comment, C.created, U.username FROM comments C, users U WHERE C.uid_fk=U.uid and C.msg_id_fk='$msg_id' order by C.com_id asc ") or die(mysql_error());
	   while($row=mysql_fetch_array($query))
	    $data[]=$row;
        if(!empty($data))
		{
       return $data;
         }
	}
	
	//Avatar Image
	public function Gravatar($uid) 
	{
	    $query = mysql_query("SELECT email FROM `users` WHERE uid='$uid'") or die(mysql_error());
	   $row=mysql_fetch_array($query);
	   if(!empty($row))
	   {
	    $email=$row['email'];
        $lowercase = strtolower($email);
        $imagecode = md5( $lowercase );
		$data="http://www.gravatar.com/avatar.php?gravatar_id=$imagecode";
		return $data;
         }
		 else
		 {
		 $data="default.jpg";
		return $data;
		 }
	}
	
	//Insert Update
	public function Insert_Update($uid, $update, $keyID, $keyType, $table) 
	{
		$update=htmlentities($update);
		$time=time();
		$ip=$_SERVER['REMOTE_ADDR'];
		
		$query = mysql_query("SELECT postID,postBody FROM `$table` WHERE userID='$uid' and keyID='$keyID' and keyType='$keyType' order by postID desc limit 1") or die(mysql_error());
		$result = mysql_fetch_array($query);
		
		if ($update!=$result['postBody']) {
			
			$query = mysql_query("INSERT INTO `$table` (userID, keyID, keyType, postBody, userIP, createDate) VALUES ('$uid', '$keyID', '$keyType', '$update', '$ip','$time')") or die(mysql_error());
			$newquery = mysql_query("SELECT M.msg_id, M.uid_fk, M.message, M.created, U.username FROM `$table` M, users U where M.uid_fk=U.uid and M.uid_fk='$uid' order by M.msg_id desc limit 1 ");
			$result = mysql_fetch_array($newquery);
			return $result;
			
		} 
		else
		{
			return false;
		}
		
		
	}
	
	//Delete update
		public function Delete_Update($uid, $msg_id) 
	{
	    $query = mysql_query("DELETE FROM `comments` WHERE msg_id_fk = '$msg_id' ") or die(mysql_error());
        $query = mysql_query("DELETE FROM `messages` WHERE msg_id = '$msg_id' and uid_fk='$uid'") or die(mysql_error());
        return true;
      	       
    }
	
	//Insert Comments
	public function Insert_Comment($uid,$msg_id,$comment) 
	{
	$comment=htmlentities($comment);
	   	    $time=time();
	   $ip=$_SERVER['REMOTE_ADDR'];
        $query = mysql_query("SELECT com_id,comment FROM `comments` WHERE uid_fk='$uid' and msg_id_fk='$msg_id' order by com_id desc limit 1 ") or die(mysql_error());
        $result = mysql_fetch_array($query);
    
		if ($comment!=$result['comment']) {
            $query = mysql_query("INSERT INTO `comments` (comment, uid_fk,msg_id_fk,ip,created) VALUES ('$comment', '$uid','$msg_id', '$ip','$time')") or die(mysql_error());
            $newquery = mysql_query("SELECT C.com_id, C.uid_fk, C.comment, C.msg_id_fk, C.created, U.username FROM comments C, users U where C.uid_fk=U.uid and C.uid_fk='$uid' and C.msg_id_fk='$msg_id' order by C.com_id desc limit 1 ");
            $result = mysql_fetch_array($newquery);
         
		   return $result;
        } 
		else
		{
		return false;
		}
       
    }
	
	//Delete Comments
		public function Delete_Comment($uid, $com_id) 
	{
	    $query = mysql_query("DELETE FROM `comments` WHERE uid_fk='$uid' and com_id='$com_id'") or die(mysql_error());
        return true;
      	       
    }

    

}

?>
