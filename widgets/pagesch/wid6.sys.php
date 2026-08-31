<?php

$schSchBody = json_decode($objs["schNote"],true);

$limit = 33;
if($schSchBody["count"]>0)
    $limit = $schSchBody["count"];



$db->join("$db_type B", "B.id=A.ceoType", "LEFT");
$db->join("$db_type C", "C.id=A.ceoCat", "LEFT");
$db->where("A.lang", $gloLang);
$db->orderBy("`ceoOrder`","asc");
$db->orderBy("`ceoID`","asc");
$workWidArr[$objs["schID"]] = $db->get("$db_ceo A", $limit, "A.*,B.name brandName,C.name typeName");

?>