<?php

$clkMenuMod = txtSec($_REQUEST["incPageType"]);
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


$path = "postpic/";

if(!(is_dir($path)))
	mkdir($path,0775);	
	
$path = "postpic/pic/";
if(!(is_dir($path)))
	mkdir($path,0775);

$path = "postpic/cache/";
if(!(is_dir($path)))
	mkdir($path,0775);

$path = "postpic/image/";
if(!(is_dir($path)))
	mkdir($path,0775);
			
	$subPage = "";
if(isset($_REQUEST["subPage"]))
	$subPage = txtSec($_REQUEST["subPage"]);
	
switch($subPage){
	
	
	case "promo":
	
		include "promo.list.sys.php";
		
	break;
	case "pro":
	
		include "pro.list.sys.php";
		
	break;
	case "proAdd":
	
		include "pro.add.sys.php";
		
	break;
	case "proEdit":
	
		include "pro.edit.sys.php";
		
	break;
	case "proDel":
		include "pro.delete.sys.php";
	break;
	case "ceoDel":
		include "ceo.delete.sys.php";
	break;
	case "visualDel":
		include "visual.delete.sys.php";
	break;
	case "promoDel":
		include "promo.del.sys.php";
	break;
	case "course":
	
		include "course.list.sys.php";
		
	break;
	case "courseAdd":
	
		include "course.add.sys.php";
		
	break;
	case "courseEdit":
	
		include "course.edit.sys.php";
		
	break;
	case "courseDel":
		include "course.delete.sys.php";
	break;	
	case "pageSch":
		include "page.sch.sys.php";
	break;
	case "pageSchList":
		$courseID = txtSec($_REQUEST["objID"]);
		include "page.sch.list.sys.php";
		include "page.sch.list.php";
		die();
	break;
	case "editPageSch":
		include "page.sch.edit.php";
	break;
	case "delPageSch":
		include "page.sch.del.sys.php";
	break;
	case "pageSubSch":
		include "page.sch.sub.sys.php";
	break;
	case "pageSubSchList":
		include "page.sch.sub.list.sys.php";
		include "page.sch.sub.list.php";
		die();
	break;	
	case "pageSubSchEdit":
		include "page.sch.sub.edit.php";
	break;
	case "pageSubSchDel":
		include "page.sch.sub.del.sys.php";
	break;
	
	case "subSch":
		include "course.sub.sys.php";
	break;
	case "courseSch":
		include "course.sch.sys.php";
	break;
	case "editSch":
		include "course.sch.edit.php";
	break;
	case "delSch":
		include "course.sch.del.sys.php";
	break;
	case "editSSch":
		include "course.sub.edit.php";
	break;
	case "delSSch":
		include "course.sub.del.sys.php";
	break;	
	case "courseSchList":
		$courseID = txtSec($_REQUEST["objID"]);
		include "course.sch.list.sys.php";
		include "course.sch.list.php";
		die();
	break;
	case "subSchList":
		include "course.sch.sub.sys.php";
		include "course.sch.sub.php";
		die();
	break;	
	case "aboutCeo":
		include "about.ceo.sys.php";
	break;
	case "destDesc":
		include "dest.desc.sys.php";
	break;
	case "visualAdd":
		include "visual.sys.php";
	break;
	default:
		
		include "map.list.sys.php";

	break;
}
?>