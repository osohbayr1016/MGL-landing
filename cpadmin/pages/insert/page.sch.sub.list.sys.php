<?php

if(isset($_REQUEST["objID"])){
	
	$selSchID=txtSec($_REQUEST["objID"]);
	
	
	$db->where("parentID", $selSchID);
	$db->orderBy("`schOrder`","asc");
	$subSchList = $db->get($db_pagesch);

	

}

?>