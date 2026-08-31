<?php
$gloUserOnline = false;

if(isset($_SESSION["upass"]) && isset($_SESSION["umail"])) {
	
	$gloLang =1;
	$thisUserMail = txtSec($_SESSION["umail"]);
	$thisUserPas = txtSec($_SESSION["upass"]);
	
	$db->where ("aname", $thisUserMail);
	$db->where ("apass", $thisUserPas);
	$onlainUserObj = $db->getOne($tbl_prefadmin);

	
	if($onlainUserObj!=null){ 
	
		
		$onlainUserName		= $onlainUserObj["name"];
		$gloUserOnlineID	= $onlainUserObj["id"];
		
		$gloUserOnline		= true;
		
	
		$db->where ("adminGroupID", $onlainUserObj["adminGroupID"]);
		$adminGroupArr = $db->getOne($tbl_admingroup,"adminGroupAction");
		$adminAccessArr = explode("-",$adminGroupArr["adminGroupAction"]);
		
		
		if(count($adminAccessArr)>0)
		foreach($adminAccessArr as $key=>$obj){
			if($obj!=""){
				$objA = explode("_",$obj);
				$adminAccessPer[$objA[0]][$objA[1]]=$objA[1];
			}
		}
		
		$db->orderBy("langOrder","asc");
		$sysLangArr = $db->get($db_lang);

		
		if(!$_SESSION["adminLang"])
			$_SESSION["adminLang"] = $sysLangArr[0]["langID"];
			
		$adminLang =  $_SESSION["adminLang"];

		foreach($sysLangArr as $key=>$obj){
			if($obj["langID"]==$adminLang)
				$selAdminLang = $obj;
		}
	}
	
}
	
?>