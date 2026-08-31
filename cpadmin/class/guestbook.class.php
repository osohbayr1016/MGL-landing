<?php
class guestClass
{
	function select_comment($table,$table_type,$feild_id,$limit){
		$where = "table_type='$table_type' and feild_id=$feild_id";
	 	$sql	= "SELECT * FROM $table	WHERE $where ORDER BY `edate` DESC  LIMIT $limit";
		$query	= mysql_query($sql);		
		while($object = mysql_fetch_object($query)){
			
			$print_comment[]=array(
					"id"			=>$object->id,
					"user_id"		=>$object->user_id,
					"title"			=>$object->title,
					"comment"		=>$object->comment,
					"ip_address"	=>$object->ip_address,
					"edate"			=>$object->edate,
					"feild_id"		=>$object->feild_id,
					"table_type"	=>$object->table_type,
					);
		}
		return $print_comment;
	}
	function select_comment_object($table,$feild_id,$feild_name){
		$where = "id='$feild_id'";
	 	$sql	= "SELECT $feild_name FROM $table WHERE $where";
		$query	= mysql_query($sql);		
		$object = mysql_fetch_object($query);
		return $object->$feild_name;
	}
	function add_newGuest($table,$value)
	{
		if (getenv(HTTP_X_FORWARDED_FOR)) {
			$IP_address = getenv(HTTP_X_FORWARDED_FOR);
		} else {
			$IP_address = getenv(REMOTE_ADDR);
		}	
					
		$title			= $value['title'];
		$user_id		= $value['user_id'];
		$comment		= $value['comment'];
		$edate			= $value['edate'];
		$table_type		= $value['table_type'];
		$feild_id		= $value['feild_id'];
		
		$sql	= "INSERT INTO `$table`(
									`id` , 
									`user_id` , 
									`edate` ,
									`title` ,
									`comment` , 
									`ip_address` ,
									`table_type` ,
									`feild_id`
									 )
							VALUES(
									'',
									'$user_id',
									'$edate',
									'$title',
									'$comment',
									'$IP_address',
									'$table_type',
									'$feild_id'							
									)";
		$query	= mysql_query($sql);	
	}
	function deletes_comment($table,$where){
		$sql	= "DELETE FROM $table WHERE $where";
		$query	= mysql_query($sql);
	}
	function comment_num($table,$table_type,$feild_id){
		$where = "table_type='$table_type' and feild_id=$feild_id";
	 	$sql	= "SELECT * FROM $table	WHERE $where and 1";
		$query	= mysql_query($sql);
		return mysql_num_rows($query);
	}
	function sendGuest_book($sendmail,$value){
		
		if (getenv(HTTP_X_FORWARDED_FOR)) {
			$IP_address = getenv(HTTP_X_FORWARDED_FOR);
		} else {
			$IP_address = getenv(REMOTE_ADDR);
		}	
			
		$name			= $value['name'];
		$title			= $value['title'];
		$mail			= $value['mail'];
		$comment		= $value['comment'];
		$edate			= $value['edate'];
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
		$headers .= "X-Mailer: PHP/".phpversion()."\r\n"; 
		$headers .= "From: ".$from."<".$fromEmail.">\r\n";
		$headers .= "Reply-to: ".$from."<".$fromEmail.">\r\n";
		
		
		$recipient = empty($to) ? $toEmail : $to."<".$toEmail.">";
		return mail($recipient, $sub, $msg, $headers);
	
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

//This function reads any file and spits out its contents in the return
function readTextFile($file){
	if(!($fp=@fopen($file,"r"))) return false;

	//Read the file
	$fileContent="";
	while (!feof($fp)) {
		$fileContent.= fgets($fp, 1024);
	}//End while loop

	//Close the file
	if(!fclose($fp)) return false;

	return($fileContent);
}//End function

//Plain text email sending function
function sendMsg($to, $toEmail, $sub, $msg, $from, $fromEmail) {
	//Compose headers for plain text email
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
	$headers .= "X-Mailer: PHP/".phpversion()."\r\n"; //The mailer name
	$headers .= "From: ".$from."<".$fromEmail.">\r\n";
	$headers .= "Reply-to: ".$from."<".$fromEmail.">\r\n";
	
	//Compose recipient
	$recipient = empty($to) ? $toEmail : $to."<".$toEmail.">";
	//return $this->save_mail($recipient, $sub, $msg, $headers);
	return mail($recipient, $sub, $msg, $headers);
}//End function

//Cleans up a string by trimming it
//if magic_quotes are on.. then stripslashes as well
function cleanUpData($data){
	$data=trim($data);
	if(get_magic_quotes_gpc()){
		$data=stripslashes($data);
	}//End if
	return($data);
}//End function

//Sends email from Flash Data
function guestEmail($postVars,$our_mail){
	
	//These variables in $postVars came from Flash
	//They are urlencoded for safe transfers
	//So we have urldecode them and then we trim them
	$name = $this->cleanUpData(urldecode($postVars["frm_name"]));
	$email = $this->cleanUpData(urldecode($postVars["frm_gmail"]));
	$message = $this->cleanUpData(urldecode($postVars["frm_comment"]));

	/*ERROR CHECKING*/
 $status = 0;
	//Email is required
	if (empty($email)) {
		//$status = "Е-майл хаягаа бичнэ vv!\n Захиа илгээгдсэнгvй";
		$status = 1;
//		die("status=".urlencode($status)."&sent=0&");
	}//End if

	//Check if email is okay..
	if (!$this->emailOK($email)) {
		//$status = "Е-майл хаяг буруу байна!\n Захиа илгээгдсэнгvй";
		$status = 2;
		//die("status=".urlencode($status)."&sent=0&");
	}//End if

	//Something in the message is required
	if (empty($message)) {
		//$status = "Та захиагаа бичнэ vv!\n Захиа илгээгдсэнгvй";
		$status = 3;
		//die("status=".urlencode($status)."&sent=0&");
	}//end if

	/*###########################*/

	$message = ereg_replace("\r", "\n", $message);
	
	//The subject line
	$subject = "Санал хүсэлт www.soddrilling.mn";
	
	//Change this to your information
	$ourName = "www.soddrilling.mn";
	$ourEmail = $our_mail;
	$ourEmail1 = $ourEmail;


	//Open the template files
	$toSenderMsg = $this->readTextFile("senderThanks.txt");
	//Replace in-built variables in the senderThanks.txt file
	$toSenderMsg = ereg_replace("\{greetings\}", empty($name) ? "Сайн уу?" : "Эрхэм хүндэт ".$name.",", $toSenderMsg);
	$toSenderMsg = ereg_replace("\{date\}", date("m/d/Y"), $toSenderMsg);
	$toSenderMsg = ereg_replace("\{message\}", $message, $toSenderMsg);

	$ourVersion = $this->readTextFile("websiteMail.txt");
	$ourVersion = ereg_replace("\{greetings\}", "Сайн уу,", $ourVersion);
	$ourVersion = ereg_replace("\{date\}", date("m/d/Y"), $ourVersion);
	$ourVersion = ereg_replace("\{name\}", empty($name) ? "none" : $name, $ourVersion);
	$ourVersion = ereg_replace("\{email\}", $email, $ourVersion);
	$ourVersion = ereg_replace("\{message\}", $message, $ourVersion);

	//Done with all formatting.. let's send our messages
	$tTasks = 2;
	$tDone = 0;
	//sendMsg function arguments?
	//sendMsg($to, $toEmail, $subject, $message, $from, $fromEmail)

	//Send a copy the website user
	if ($this->sendMsg($name, $email, $subject, $toSenderMsg, $ourName, $ourEmail1)) $tDone++;

	//Send a copy to ourselves
	if ($this->sendMsg($ourName, $ourEmail, $subject, $ourVersion, empty($name) ? $email : $name, $email)) $tDone++;

	if ($tDone == $tTasks) {
		$status = 1;
	} else {
		$status = 2;
	}//End if
	
	return $status;
}//End function
	
}
?>