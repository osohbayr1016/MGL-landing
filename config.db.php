<?php
  // ini_set("session.gc_maxlifetime","3600");
//   ini_set('date.timezone', 'Asia/Ulaanbaatar');
  // ini_set('session.save_path',$temp_dir."sessions");
   

  // import_request_variables("gp","");
   ini_set("display_errors","0");
   $db = new MysqliDb($sysDbHost,$sysDbUser,$sysDbPass, $sysDbName);
   
   $thisGuestID = session_id();

?>