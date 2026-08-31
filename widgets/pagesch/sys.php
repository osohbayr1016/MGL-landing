<?php 

 $widColorArr = ["#fe5925","#46877e","#fbba34","#fe5925","#fe5925","#fe5925"];
	

	$db->where ("schKey", $pageID);
	$db->orderBy("schOrder","asc");
	$schArr = $db->get($db_pagesch);
	
	$allSchArr = formatTree($schArr, 0, "parentID", "schID");
	
	if(count($allSchArr)>0){
		foreach($allSchArr as $keys=>$objs){
			
			$incSys = "wid".$objs["schTemp"].".sys.php";
			if(is_file("widgets/pagesch/".$incSys))
				include $incSys;
			
			
			
		}
	} 
	
//	$widJsArr["pagesch"] = $gloConstWidDir."pagesch/filter.js.php";

?>