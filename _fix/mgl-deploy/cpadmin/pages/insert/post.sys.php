<?php

include_once __DIR__ . "/order.helper.php";

$sysReturnLink =  "/insert/maps";

switch($_POST["frmPost"]){
	
	case "prodPost":
		
		$path = "postpic/image/news/";

		if(!(is_dir($path)))
			mkdir($path,0775);	
		
		
		$proPic = "";
		
		$newsCmd = "n";
		
		if(isset($_POST["frmCmd"]))
			$newsCmd = "y";
		
		$newsTop = txtSec($_POST["frmTop"]);
		$newsSep = txtSec($_POST["frmSep"]);
		
		
		if($newsTop>0){

			$post_value_arr["newsTop"]	=	"";
			$db->where ('newsTop',$newsTop);
			$db->update($db_newsroll, $post_value_arr);

		}
		if($newsSep!=''){

			$post_value_arr["newsSep"]	=	"n";
			$db->where ('newsSep',$newsSep);
			$db->update($db_newsroll, $post_value_arr);
		}

		
		
		$newsCat = txtSec($_POST["frmCat"]);
		$subCat = txtSec($_POST["frmSubCat"]); 
		
		$db->where('id', $newsCat);
		$catName = $db->getValue($db_type,"name");

		$db->where('id', $subCat);
		$subCatName = $db->getValue($db_type,"name");
		
		
		$post_value_arr["newsTitle"]	=	txtSec($_POST["frmTitle"]);
		$post_value_arr["newsCatID"]	=	$newsCat;	
		$post_value_arr["newsCat"]		=	$catName;	
		$post_value_arr["newsSubCat"]	=	$subCat;
		$post_value_arr["newsSubCatn"]	=	$subCatName;			
		$post_value_arr["newsType"]		=	txtSec($_POST["frmType"]);	
		$post_value_arr["newsSep"]		=	$newsSep;	
		$post_value_arr["cmdIs"]		=	$newsCmd;	
		$post_value_arr["newsDesc"]		=	txtSec($_POST["frmDesc"]);
		
		if($_FILES["frmNewsPic"]["tmp_name"]!="")
		$post_value_arr["newsPic"]		=	"";
		$post_value_arr["newsVid"]		=	str_replace($gloDomainLink, '', $_POST["frmVidUrl"]);
		$post_value_arr["newsTop"]		=	$newsTop;
		
		
		
		if($_POST["frmEditID"]>0){
			
			$newsID = txtSec($_POST["frmEditID"]);
			$post_value_arr["updateDate"]	=	time();

			$db->where ('newsID',$newsID);
			$db->update($db_newsroll, $post_value_arr);

		
		}
		else{			
			
			$post_value_arr["lang"]		=	$adminLang;
			$post_value_arr["createDate"]	=	time();
			
			$newsID = $db->insert($db_newsroll, $post_value_arr);
			
			
		}
		
		$db->where('newsID', $newsID);
		$db->delete($db_newsmore);
		
		$bodyContent = addslashes($_POST["frmBody"]); 
		
		
		unset($post_value_arr);
		$post_value_arr["newsID"]		=	$newsID;
		$post_value_arr["newsBody"]		=	$bodyContent;
		$post_value_arr["newsVideo"]	=	addslashes($_POST["frmVideo"]);
		
		$db->insert($db_newsmore, $post_value_arr);
		
		
		
		if($_FILES["frmNewsPic"]["tmp_name"]!=""){
			
								

			$fileName = $newsID.".jpg";
			$to = $path.$fileName;
			
			if (file_exists($to))
			{
				unlink($to);
			}

			$from = $_FILES["frmNewsPic"]["tmp_name"];						

			if(move_uploaded_file($from,$to))	
				$proLogo = "";

		}
		
		
		
		
		$sysReturnLink =  "/insert/pro/".txtSec($_POST["frmCat"]);
		
	
	break;	
	
	case "coursePost":
		
		
		$bodyContent = addslashes($_POST["frmBody"]);
		
		
		$mainClass->tableClass=$db_course;

		$tourPrice = 0;

		if(count($_POST["frmPriceVal"])>0)
		foreach($_POST["frmPriceVal"] as $key=>$price){
			if($price!=""){
				if($price>0 and $key<1)
					$tourPrice = $price;

				if($price<$tourPrice)
					$tourPrice = $price;

			}
		
		}
		$checkBox = "n";
		if(isset($_POST["frmStatus"]))
			$checkBox = $_POST["frmStatus"];

		unset($post_value_arr);
		$post_value_arr["courseType"]	=	"|".join("|",$_POST["frmDest"])."|";
		$post_value_arr["tourInc"]	=	"|".join("|",$_POST["frmInc"])."|";
		$post_value_arr["tourNotInc"]	=	"|".join("|",$_POST["frmNotInc"])."|";
		$post_value_arr["tourPriceInd"]	=	"|".join("|",$_POST["frmPriceInd"])."|";
		$post_value_arr["tourPriceVal"]	=	"|".join("|",$_POST["frmPriceVal"])."|";
		$post_value_arr["courseTitle"]	=	txtSec($_POST["frmTitle"]);
		$post_value_arr["tourPrice"]	=	$tourPrice;
		$post_value_arr["isPublish"]	=	$checkBox;
		$post_value_arr["courseNote"]	=	txtSec($_POST["frmDesc"]);
		$post_value_arr["menuID"]	=	txtSec($_POST["frmCat"]);
		$post_value_arr["courseBody"]	=	$bodyContent;
		$post_value_arr["tourKm"]	=	txtSec($_POST["frmKm"]);
		$post_value_arr["tourCar"]	=	txtSec($_POST["frmCar"]);
		$post_value_arr["coursePic"]	=	str_replace($gloDomainLink, '', $_POST["frmPicUrl"]);
		
		
		if($_POST["frmEditID"]>0){
			
			$newsID = txtSec($_POST["frmEditID"]);
			$post_value_arr["editID"]	=	$newsID;
			$mainClass->valueClass = $post_value_arr;
			$mainClass->editClass("courseID");
		
		}
		else{			
			
			$post_value_arr["lang"]		=	$adminLang;
			$mainClass->valueClass = $post_value_arr;
			
			$mainClass->addClass();
			
			$newsID = mysql_insert_id();
			
		}
		
		$mainClass->tableClass=$db_coursesch;
		
		unset($post_value_arr);
		$post_value_arr["schKey"]	=	$newsID;
		$post_value_arr["editID"]	=	$gloSessionID;
		$mainClass->valueClass = $post_value_arr;
		$mainClass->editClass("schKey");
		
		
		
		$sysReturnLink =  "/insert/course/".txtSec($_POST["frmCat"]);
		
	
	break;
	case "ceoPost":
		
		$path = "postpic/ceo/";

		if(!(is_dir($path)))
			mkdir($path,0775);
			
			
		$proPic = "";
		
		$fPic = "";
		
		$adsPicName = date("ymdhis").random_str(20);
		
	
		if(count($_FILES["frmSlide"]["error"])>0)	
		foreach ($_FILES["frmSlide"]["error"] as $key => $error){				
			
			
			if($_FILES["frmSlide"]["tmp_name"][$key]!=""){

				if ($error == UPLOAD_ERR_OK){						

					$fileName = $adsPicName."-".$key.".jpg";						
					
					if($fPic=="")
						$fPic = $fileName;
					
					$to = $path.$fileName;

					$from = $_FILES["frmSlide"]["tmp_name"][$key];						

					if(move_uploaded_file($from,$to))	
						$proPic .= $fileName.":";
					

				}
				
				if($_POST["frmOldPics"][$key]!="")					
					unlink($path.$_POST["frmOldPics"][$key]);

			}			
			elseif($_POST["frmOldPics"][$key]!=""){
				if($_POST["adsPicDel"][$key]!="d")
					$proPic .= $_POST["frmOldPics"][$key].":";
				else
					unlink($path.$_POST["frmOldPics"][$key]);
				
			}

		}
	
		$typeOrder = max(1, (int)txtSec($_POST["frmOrder"]));
		$slideType = txtSec($_POST["frmSlideType"]);
		$menuID = (int)txtSec($_POST["frmMenuID"]);
		
		unset($post_value_arr);
		$post_value_arr["ceoName"]		=	txtSec($_POST["frmTitle"]);
		$post_value_arr["lang"]		=	$adminLang;
		$post_value_arr["ceoType"]	=	"|".join("|",$_POST["ceoType"])."|";
		$post_value_arr["proType"]	=	"|".join("|",$_POST["proType"])."|";
		$post_value_arr["ceoCat"]	=	txtSec($_POST["frmCat"]);
		$post_value_arr["ceoStart"]	=	txtSec($_POST["frmStart"]);
		$post_value_arr["ceoEnd"]	=	txtSec($_POST["frmEnd"]);
		$post_value_arr["ceoStatus"]=	txtSec($_POST["frmStatus"]);
		$post_value_arr["ceoClient"]=	txtSec($_POST["frmClient"]);
		$post_value_arr["ceoSize"]	=	txtSec($_POST["frmSize"]);
		$post_value_arr["ceoTeam"]	=	txtSec($_POST["frmTeam"]);
		$post_value_arr["ceoSlide"]	=	$proPic;
		$post_value_arr["ceoDesc"]	=	$_POST["frmDesc"];
		$post_value_arr["ceoBody"]	=	$_POST["frmNote"];
		$post_value_arr["ceoPic"]	=	$_POST["frmPic"];
		$post_value_arr["ceoOrder"]	=	$typeOrder;
		
		if($_POST["frmEditID"]>0){
			
			$slideID = (int)txtSec($_POST["frmEditID"]);

			$db->where ('ceoID',$slideID);
			$db->update($db_ceo, $post_value_arr);
			
		
		}
		else{
			
			$post_value_arr["pageID"]	=	$menuID;
			
			$slideID = $db->insert($db_ceo, $post_value_arr);
			
		}

		$scope = array("lang" => $adminLang);
		reorderScopedItem($db, $db_ceo, "ceoID", "ceoOrder", $slideID, $typeOrder, $scope);
		
		$sysReturnLink =  "/insert/promo/".$menuID;
		
	
	break;
	case "ceoReorder":
		include "ceo.reorder.sys.php";
	break;
	case "ceoOrderSet":
		$ceoID = (int)txtSec($_POST["ceoID"]);
		$newOrder = max(1, (int)txtSec($_POST["frmOrder"]));
		$scope = array("lang" => $adminLang);
		reorderScopedItem($db, $db_ceo, "ceoID", "ceoOrder", $ceoID, $newOrder, $scope);
		orderAjaxDone(array("order" => $newOrder));
	break;
	case "schOrderSet":
		$schID = (int)txtSec($_POST["schID"]);
		$newOrder = max(1, (int)txtSec($_POST["frmOrder"]));
		$parentID = txtSec($_POST["parentID"]);
		if ($parentID !== "" && (int)$parentID > 0) {
			$scope = array("parentID" => (int)$parentID);
		} else {
			$scope = array(
				"schKey" => (int)txtSec($_POST["schKey"]),
				"parentID" => "0"
			);
		}
		reorderScopedItem($db, $db_pagesch, "schID", "schOrder", $schID, $newOrder, $scope);
		orderAjaxDone(array("order" => $newOrder));
	break;
	case "homeProjectsSave":
		include "home.projects.save.php";
	break;
	case "homeProjectPic":
		include "home.projects.pic.php";
	break;
	case "visualPost":
		
		$path = "postpic/visual/";

		if(!(is_dir($path)))
			mkdir($path,0775);
			
			
		$proPic = "";
		
		$fPic = "";
		
		$adsPicName = date("ymdhis").random_str(20);
		
	
		if(count($_FILES["frmSlide"]["error"])>0)	
		foreach ($_FILES["frmSlide"]["error"] as $key => $error){				
			
			
			if($_FILES["frmSlide"]["tmp_name"][$key]!=""){

				if ($error == UPLOAD_ERR_OK){						

					$fileName = $adsPicName."-".$key.".jpg";						
					
					if($fPic=="")
						$fPic = $fileName;
					
					$to = $path.$fileName;

					$from = $_FILES["frmSlide"]["tmp_name"][$key];						

					if(move_uploaded_file($from,$to))	
						$proPic .= $fileName.":";
					

				}
				
				if($_POST["frmOldPics"][$key]!="")					
					unlink($path.$_POST["frmOldPics"][$key]);

			}			
			elseif($_POST["frmOldPics"][$key]!=""){
				if($_POST["adsPicDel"][$key]!="d")
					$proPic .= $_POST["frmOldPics"][$key].":";
				else
					unlink($path.$_POST["frmOldPics"][$key]);
				
			}

		}
		
		
		unset($post_value_arr);
		$post_value_arr["visualTitle"]	=	$_POST["frmTitle"];
		$post_value_arr["lang"]		=	$adminLang;
		$post_value_arr["visualTools"]	=	"|".join("|",$_POST["frmTools"])."|";
		$post_value_arr["visualFields"]	=	"|".join("|",$_POST["frmField"])."|";
		$post_value_arr["visualDesigner"]	=	txtSec($_POST["frmDesigner"]);
		$post_value_arr["visualTags"]	=	$_POST["frmTags"];
		$post_value_arr["visualVideo"]	=	$_POST["frmEmbed"];
		$post_value_arr["visualSlide"]	=	$proPic;
		
		
		if($_POST["frmEditID"]>0){
			
			$slideID = txtSec($_POST["frmEditID"]);
			$db->where ('visualID',$slideID);
			$db->update($db_visual, $post_value_arr);
			
		
		}
		else{
			
			$post_value_arr["visualDate"]	=	date("Y-m-d H:i:s");
			$post_value_arr["pageID"]	=	$_POST["frmMenuID"];
			
			$slideID = $db->insert($db_visual, $post_value_arr);
			
		}
		
		if($_FILES["frmPic"]["tmp_name"]!=""){
			
								

			$fileName = $slideID.".jpg";			

			$to = $path.$fileName;
			
			if (file_exists($to))
			{
				unlink($to);
			}

			$from = $_FILES["frmPic"]["tmp_name"];						

			if(move_uploaded_file($from,$to))	
				$proLogo = "";

		}
		
		
		
		$sysReturnLink =  "/insert/promo/".txtSec($_POST["frmMenuID"]);
		
	
	break;
	case "courseSubSch":
		
		
		$mainClass->tableClass=$db_coursesch;
		
		$bodyContent = addslashes($_POST["frmNote"]);
		$bodyContent = str_replace('cssNewsMore', '', $bodyContent); 
		$bodyContent = str_replace('newsContent', '', $bodyContent); 
		
		unset($post_value_arr);
		$post_value_arr["schTitle"]		=	txtSec($_POST["frmName"]);
		$post_value_arr["schNote"]		=	$bodyContent;
		$post_value_arr["schOrder"]		=	txtSec($_POST["frmOrder"]);
		$post_value_arr["parentID"]		=	txtSec($_POST["frmSchID"]);
		
		if($_POST["frmEditID"]>0){
			
			$schID 						= txtSec($_POST["frmEditID"]);
			$post_value_arr["editID"]	=	$schID;
			$mainClass->valueClass 		= $post_value_arr;
			$mainClass->editClass("schID");
		
		}
		else{			
			
			$post_value_arr["schKey"]	=	txtSec($_POST["frmMMID"]);
			$mainClass->valueClass = $post_value_arr;
			$mainClass->addClass();
			
			$pageID = mysql_insert_id();
			
		}
		
		
		$sysReturnLink =  "/insert/subSchList/".txtSec($_POST["frmSchID"]);
		
		
	break;
	case "courseSch":
		
		
		$mainClass->tableClass=$db_coursesch;
		
		$schImage = "";
		foreach($_POST["frmPicUrl"] as $key=>$obj){
			if($obj!="")
				$schImage .= str_replace($gloDomainLink, '', $obj)."|";
		}
	
		unset($post_value_arr);
		$post_value_arr["schTitle"]		=	txtSec($_POST["frmName"]);
		$post_value_arr["schNote"]		=	$_POST["frmNote"];
		$post_value_arr["schOrder"]		=	txtSec($_POST["frmOrder"]);
		$post_value_arr["schLat"]		=	txtSec($_POST["frmLat"]);
		$post_value_arr["schLon"]		=	txtSec($_POST["frmLon"]);
		$post_value_arr["schImage"]		=	$schImage;
		
		if($_POST["frmEditID"]>0){
			
			$schID 						= txtSec($_POST["frmEditID"]);
			$post_value_arr["editID"]	=	$schID;
			$mainClass->valueClass 		= $post_value_arr;
			$mainClass->editClass("schID");
		
		}
		else{			
			$schKey = $gloSessionID;
			if($_POST["frmCourseID"]>0)
				$schKey = txtSec($_POST["frmCourseID"]);
				
			$post_value_arr["schKey"]	=	$schKey;
			$mainClass->valueClass = $post_value_arr;
			$mainClass->addClass();
			
			$pageID = mysql_insert_id();
			
		}
		
		
		$sysReturnLink =  "/insert/courseSchList/".txtSec($_POST["frmCourseID"]);
		
		
	break;	
	case "pageSch":
		
		
		unset($post_value_arr);
		$post_value_arr["schNote"]		=	json_encode($_POST["frmVal"],JSON_UNESCAPED_UNICODE);
		$typeOrder						=	max(1, (int)txtSec($_POST["frmOrder"]));
		$post_value_arr["schOrder"]		=	$typeOrder;
		$post_value_arr["schTemp"]		=	txtSec($_POST["frmTemplate"]);
		
		if($_POST["frmEditID"]>0){
			
			$schID 						= (int)txtSec($_POST["frmEditID"]);
			$db->where("schID", $schID);
			$existingSch = $db->getOne($db_pagesch, "schKey, parentID");
			$db->where ('schID',$schID);
			$db->update($db_pagesch, $post_value_arr);
		
		}
		else{			
			$schKey = $gloSessionID;
			if($_POST["frmCourseID"]>0)
				$schKey = (int)txtSec($_POST["frmCourseID"]);
				
			$post_value_arr["schKey"]	=	$schKey;
			$post_value_arr["parentID"]	=	"0";
			
			$schID = $db->insert($db_pagesch, $post_value_arr);
			$existingSch = array("schKey" => $schKey, "parentID" => "0");
			
			
		}

		reorderScopedItem($db, $db_pagesch, "schID", "schOrder", $schID, $typeOrder, array(
			"schKey" => (int)$existingSch["schKey"],
			"parentID" => "0"
		));
		
		$sysReturnLink =  "/insert/pageSchList/".txtSec($_POST["frmCourseID"]);
		
		
	break;
	case "pageSubSch":
		
		
		unset($post_value_arr);
		$typeOrder						=	max(1, (int)txtSec($_POST["frmOrder"]));
		$post_value_arr["schOrder"]		=	$typeOrder;
		$post_value_arr["parentID"]		=	txtSec($_POST["frmSchID"]);
		$post_value_arr["schNote"]		=	json_encode($_POST["frmVal"],JSON_UNESCAPED_UNICODE);
		
		if($_POST["frmEditID"]>0){
			
			$schID 						= (int)txtSec($_POST["frmEditID"]);
			$db->where ('schID',$schID);
			$db->update($db_pagesch, $post_value_arr);
		
		}
		else{			
			
			$post_value_arr["schKey"]	=	txtSec($_POST["frmMMID"]);
			$schID = $db->insert($db_pagesch, $post_value_arr);
			
			
		}

		reorderScopedItem($db, $db_pagesch, "schID", "schOrder", $schID, $typeOrder, array(
			"parentID" => (int)txtSec($_POST["frmSchID"])
		));
		
		$sysReturnLink =  "/insert/pageSubSchList/".txtSec($_POST["frmSchID"]);
		
		
	break;	
	case "ceoDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);

		$db->where('ceoID', $delID);
		$db->delete($db_ceo);
		
		die();
		
	
	break;
	case "visualDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);

		$db->where('visualID', $delID);
		$db->delete($db_visual);
		
		die();
		
	
	break;
	case "schDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);
		

		$db->where('schID', $delID);
		$db->delete($db_coursesch);
		
		die();
		
	
	break;
	case "schPageDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);
		

		$db->where('schID', $delID);
		$db->orWhere ('parentID', $delID);
		$db->delete($db_pagesch);
		
		die();
		
	
	break;
	case "courseDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);

		$db->where('schKey', $delID);
		$db->delete($db_coursesch);

		$db->where('courseID', $delID);
		$db->delete($db_course);
		
		
		$sysReturnLink =  "/insert/course/".txtSec($_POST["frmMenuID"]);
		
	
	break;
	case "proDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);
		
		$typeID = txtSec($_POST["frmMainType"]);

		$db->where('newsID', $delID);
		$db->delete($db_newsroll);

		$db->where('newsID', $delID);
		$db->delete($db_newsmore);
		
		
		$sysReturnLink =  "/insert/pro/".$typeID;
		
	
	break;
	
}


?>