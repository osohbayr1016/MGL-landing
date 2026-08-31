<?php


$db->orderBy("`createDate`","DESC");
$db->where("lang", $gloLang);
$newsWidArr[$objs["schID"]] = $db->get($db_newsroll, $limit);

?>