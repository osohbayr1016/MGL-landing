<?php
$objID = 0;

if(isset($_REQUEST["objID"]))
	$objID=txtSec($_REQUEST["objID"]);
		
$widJsArr["mapedit"] = $clkMenuModDir."pro.edit.js.php";

$incPageUrl = $clkMenuModDir."pro.edit.frm.php";
	
$postPath = "postpic/";

if(!(is_dir($postPath)))
	mkdir($postPath,0775);
	
$postPath = "postpic/image/";

if(!(is_dir($postPath)))
	mkdir($postPath,0775);
			
$selProSpeArr[0] = 0;

if($objID>0){
	
	$editID = $objID;

	$db->join("$db_newsmore B", "B.newsID=A.newsID", "LEFT");
	$db->where("A.newsID", $editID);
	$selNewObj = $db->getOne("$db_newsroll A", null, "A.*,B.*");

	
	$selMainCat = $selNewObj["newsCatID"];	
	

}

$db->where("menu_menu", 0);
$db->where("type", "worktype");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$typesArr = $db->get($db_type);


$db->where("menu_menu", $selMainCat);
$db->orderBy("`order`","asc");
$subTypesArr = $db->get($db_type);

?>