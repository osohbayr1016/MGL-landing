<?php
	
	
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		
$widJsArr["districtedit"] = $clkMenuModDir."lang.edit.js.php";

$incPageUrl = $clkMenuModDir."lang.edit.frm.php";

	
if($selTypeID>0){
	
	$db->where("langID", $selTypeID);
	$selTypeObj = $db->getOne($db_lang);

	$lastTypeOrder = $selTypeObj["langOrder"];
			
}
else{

	$db->orderBy("langOrder","asc");
	$oldLangArr = $db->get($db_lang);


	$db->orderBy("langOrder","DESC");
	$lastTypeOrder = $db->getValue($db_lang,"langOrder",1) + 1;

	
	
}
?>