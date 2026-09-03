<?php

$clkMenuMod = "projects";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";

if(isset($_REQUEST["productID"])){

	$selProductID = txtSec($_REQUEST["productID"]);

	require_once $gloConstWidDir."pagesch/ceo-order.php";

	$db->join("$db_type B", "B.id=A.ceoType", "LEFT");
	$db->join("$db_type C", "C.id=A.ceoCat", "LEFT");
	$db->where("A.ceoID", $selProductID);
	$productObj = $db->getOne("$db_ceo A", "A.*, B.name brandName, C.name typeName");

	$selProPics = array();
	if($productObj["ceoSlide"]!=""){
		foreach(explode(":", $productObj["ceoSlide"]) as $imgurl){
			$imgurl = trim($imgurl);
			if($imgurl!=""){
				$selProPics[] = $imgurl;
			}
		}
	}

	if(count($selProPics)>0){
		$pdetailHero = picUrl("ceo",$selProPics[0]);
		$pdetailGallery = array_slice($selProPics, 1, 2);
	}else{
		$pdetailHero = newsPicFnc($productObj["ceoID"], $productObj["ceoPic"]);
		$pdetailGallery = array();
	}

	$productTypeArr = array();
	if($productObj["proType"]!=""){
		$selProTypes = explode("|", $productObj["proType"]);
		$db->where("id", $selProTypes, "IN");
		$db->orderBy("`order`", "asc");
		$productTypeArr = $db->get($db_type);
	}

	$proTypeLabel = "";
	if(count($productTypeArr)>0){
		$names = array();
		foreach($productTypeArr as $row){
			$names[] = $row["name"];
		}
		$proTypeLabel = join(", ", $names);
	}

	$pdetailYear = trim($productObj["ceoStart"]);
	if($productObj["ceoEnd"]!="" && $productObj["ceoEnd"]!=$productObj["ceoStart"]){
		$pdetailYear .= "–".$productObj["ceoEnd"];
	}

	$pdetailFacts = array();
	$pdetailClient = isset($productObj["ceoClient"]) ? trim($productObj["ceoClient"]) : "";
	if($pdetailClient!=""){
		$pdetailFacts[] = array("label" => "Client", "value" => $pdetailClient);
	}
	if($pdetailYear!=""){
		$pdetailFacts[] = array("label" => "Year", "value" => $pdetailYear);
	}
	if(trim($productObj["ceoSize"])!=""){
		$pdetailFacts[] = array("label" => "Size", "value" => $productObj["ceoSize"]);
	}
	if(trim($productObj["ceoStatus"])!=""){
		$pdetailFacts[] = array("label" => "Status", "value" => $productObj["ceoStatus"]);
	}
	if($proTypeLabel!=""){
		$pdetailFacts[] = array("label" => "Role", "value" => $proTypeLabel);
	}elseif(trim($productObj["ceoTeam"])!=""){
		$pdetailFacts[] = array("label" => "Team", "value" => $productObj["ceoTeam"]);
	}

	$allProjects = sortCeoRowsByOrder(fetchCeoProjectsForLang($db, $db_ceo, $db_type, $gloLang));
	$pdetailPrev = null;
	$pdetailNext = null;
	foreach($allProjects as $i => $row){
		if((int)$row["ceoID"]===(int)$selProductID){
			if($i>0){
				$pdetailPrev = $allProjects[$i-1];
			}
			if($i<count($allProjects)-1){
				$pdetailNext = $allProjects[$i+1];
			}
			break;
		}
	}

	$db->where("pageType", $clkMenuMod);
	$db->where("lang", $gloLang);
	$db->where("menu_menu", 0);
	$clkMenuObj = $db->getOne($tbl_main_menu);

	$allWidgetArr = array("projectmore");
	$incPageUrl = $clkMenuModDir."more.php";

}else{

	$db->where("pageType", $clkMenuMod);
	$db->where("lang", $gloLang);
	$db->where("menu_menu", 0);
	$clkMenuObj = $db->getOne($tbl_main_menu);

	$pageID = $clkMenuObj["id"];

	$allWidgetArr = array("pagesch");
	$incPageUrl = $clkMenuModDir."projects.php";
}
?>
