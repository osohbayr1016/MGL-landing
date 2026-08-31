<?php

$clkMenuMod = "clientarea";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";

include "client.info.php";

if($gloUserOnline){

    if(!isset($_REQUEST["subPage"]))
        $_REQUEST["subPage"] = "def";
    switch($_REQUEST["subPage"]){
        case "logout":
			    setcookie("cmail","",time()-3600);
			  setcookie("cpass","",time()-3600);
			  session_destroy();
			  header("location: /");
		break;
        case "filedownload":
            
            $fileID = txtSec($_REQUEST["fileID"]);

            $db->where("id", $fileID);
            $db->where("userID", $gloUserOnlineID);
            $downloadFile = $db->getOne($db_files);

            if($downloadFile!=null){

                $fileFrame = $downloadFile["fileLink"]; 
               // $incPageUrl = $clkMenuModDir."download.php";
               header("location: ".$fileFrame);
                
            }
            else
                header("location: /clientarea");

            

        break;
        case "changepass":


            $incPageUrl = $clkMenuModDir."changepass.php";

        
        break;
        default:
            $db->orderBy("createDate","desc");
            $db->where ("userID", $gloUserOnlineID);
            $fileList = $db->get($db_files); 

            $incPageUrl = $clkMenuModDir."files.php";

        
        break;
        
    }
    

    $gloIncHomePage = "client.php";

}
else
    $gloIncHomePage = "client.login.php";

?>