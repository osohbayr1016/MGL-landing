<?php

$schSchBody = json_decode($objs["schNote"],true);

$limit = 12;
if($schSchBody["count"]>0)
    $limit = $schSchBody["count"];

    
    $db->where("lang", $gloLang);
    $db->where("type",'pro');
    $db->orderBy("`order`","asc");
    $workLocArr = $db->get($db_type);
    
    $db->where("lang", $gloLang);
    $db->where("type",'protype');
    $db->orderBy("`order`","asc");
    $workTypeArr = $db->get($db_type);

    $db->where("lang", $gloLang);
    $db->where("type",'person');
    $db->orderBy("`order`","asc");
    $workSecArr = $db->get($db_type);
    
    
    $db->where("lang", $gloLang);
    $db->orderBy("`ceoStatus`","asc");
    $db->groupBy ("ceoStatus");
    $workStatusArr = $db->get($db_ceo, null, "ceoStatus");


    $db->orderBy("`ceoOrder`","asc");
    $db->orderBy("`ceoID`","asc");
	$db->join("$db_type C", "C.id=A.ceoCat", "LEFT");
	$db->where("A.lang", $gloLang);
	$workWidArr[$objs["schID"]] = $db->get("$db_ceo A", null, "A.*,C.name typeName");


    $widJsArr["pagesch"] = $gloConstWidDir."pagesch/filter.js.php";
?>