<?php 

 $widColorArr = ["#fe5925","#46877e","#fbba34","#fe5925","#fe5925","#fe5925"];
	

	$db->where ("schKey", $pageID);
	$db->orderBy("schOrder","asc");
	$schArr = $db->get($db_pagesch);
	
	$allSchArr = formatTree($schArr, 0, "parentID", "schID");

	/* "Холбоо барих" sub area-г бусад section-үүдийн хамгийн сүүлд байрлуулна.
	   Section-ийн бие, дээд талын sub area menu, ScrollMagic scene гурвуулаа
	   $allSchArr-аас уншдаг тул энд нэг удаа дараалал сольвоход гурвуулаа
	   нийцэж гарна. Гарчгаар нь тааруулж байгаа учир schID хатуу бичихгүй,
	   хэл тус бүрийн about page дээр адилхан ажиллана. */
	if(isset($clkMenuMod) and $clkMenuMod=="about" and count($allSchArr)>1){
		$schLastTitleArr = array("холбоо барих","Холбоо барих","contact","contact us");
		$schKeepArr = array();
		$schLastArr = array();
		foreach($allSchArr as $keys=>$objs){
			$selSchNote  = json_decode($objs["schNote"],true);
			$selSchTitle = isset($selSchNote["title"]) ? $selSchNote["title"] : "";
			$selSchTitle = trim(preg_replace("/\s+/u"," ",strip_tags($selSchTitle)));
			if(in_array($selSchTitle,$schLastTitleArr) or in_array(strtolower($selSchTitle),$schLastTitleArr))
				$schLastArr[$keys] = $objs;
			else
				$schKeepArr[$keys] = $objs;
		}
		if(count($schLastArr)>0)
			$allSchArr = $schKeepArr + $schLastArr;
	}
	
	if(count($allSchArr)>0){
		foreach($allSchArr as $keys=>$objs){
			
			$incSys = "wid".$objs["schTemp"].".sys.php";
			if(is_file("widgets/pagesch/".$incSys))
				include $incSys;
			
			
			
		}
	} 
	
//	$widJsArr["pagesch"] = $gloConstWidDir."pagesch/filter.js.php";

?>