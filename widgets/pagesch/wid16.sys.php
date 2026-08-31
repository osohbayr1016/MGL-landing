<?php


$db->where("lang", $gloLang);
    $db->where("type",'pro');
    $db->orderBy("`order`","asc");
    $workLocArr = $db->get($db_type);
    
    $db->where("lang", $gloLang);
    $db->where("type",'fields');
    $db->orderBy("`order`","asc");
    $workTypeArr = $db->get($db_type);

    $db->where("lang", $gloLang);
    $db->where("type",'tools');
    $db->orderBy("`order`","asc");
    $workSecArr = $db->get($db_type);
    
    
    $db->where("lang", $gloLang);
    $db->orderBy("`ceoStatus`","asc");
    $db->groupBy ("ceoStatus");
    $workStatusArr = $db->get($db_ceo, null, "ceoStatus");

	
	$db->orderBy("A.visualDate","DESC");
    $db->join("$db_pagesch B", "B.schID=A.visualDesigner", "LEFT");
	$db->where("A.lang", $gloLang);
	$workWidArr[$objs["schID"]] = $db->get("$db_visual A", null, "A.*,B.schTitle designerName");


$widJsArr["pagesch"] = $gloConstWidDir."pagesch/filter.js.php";

?>