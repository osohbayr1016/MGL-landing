<?php

$schSchBody = json_decode($objs["schNote"],true);

$limit = 12;
if($schSchBody["count"]>0)
    $limit = $schSchBody["count"];



$db->where("lang", $gloLang);
$newsWidArr[$objs["schID"]] = $db->get($db_newsroll, $limit);


?>