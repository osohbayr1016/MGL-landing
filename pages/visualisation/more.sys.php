<?php

$selProductID = txtSec($_REQUEST["productID"]);

$db->orderBy("A.visualDate","DESC");
$db->join("$db_pagesch B", "B.schID=A.visualDesigner", "LEFT");
$db->where("A.visualID", $selProductID);
$productObj = $db->getOne("$db_visual A", null, "A.*,B.schTitle designerName");

$selProPics = explode(":",$productObj["visualSlide"]);


$db->orderBy("A.visualDate","DESC");
$db->join("$db_pagesch B", "B.schID=A.visualDesigner", "LEFT");
$db->where("A.lang", $gloLang);
$db->where("A.visualID", $selProductID,"!=");
$randomVisualArr = $db->get("$db_visual A", 4, "A.*,B.schTitle designerName");



$allWidgetArr = array("visualisationmore");

?>