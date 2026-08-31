<?php

$sysReturnLink =  "/settings";

switch($_POST["frmPost"]){
	case "edit":
		
	
	
		unset($post_value_arr);
		$post_value_arr["site_name"]	=	txtSec($_POST["frmTitle"]);
		$post_value_arr["adminMail"]	=	txtSec($_POST["frmMail"]);
		$post_value_arr["keywords"]		=	txtSec($_POST["frmKeys"]);
		$post_value_arr["siteDes"]		=	txtSec($_POST["frmDes"]);

		$post_value_arr["socialFB"]		=	$_POST["frmFB"];
		$post_value_arr["socialTW"]		=	$_POST["frmTW"];
		$post_value_arr["socialYT"]		=	$_POST["frmYT"];
		$post_value_arr["socialIN"]		=	$_POST["frmIN"];
		$post_value_arr["socialTR"]		=	$_POST["frmTR"];
		$post_value_arr["socialWC"]		=	$_POST["frmWC"];

		$post_value_arr["socialVB"]		=	$_POST["frmVB"];
		$post_value_arr["socialTL"]		=	$_POST["frmTL"];
		$post_value_arr["socialWS"]		=	$_POST["frmWS"];
		$post_value_arr["socialPhone"]	=	$_POST["frmPH"];

		
		$db->where ('id', txtSec($_POST["frmEditID"]));
		$db->update($tbl_config, $post_value_arr);
		
		
	break;

	case "langPost":
		
		$path = "postpic/lang/";

		if(!(is_dir($path)))
			mkdir($path,0775);
		
	
		$typeOrder = txtSec($_POST["frmOrder"]);
		
		unset($post_value_arr);
		$post_value_arr["langName"]		=	$_POST["frmName"];
		$post_value_arr["langKey"]		=	txtSec($_POST["frmKey"]);
		$post_value_arr["langOrder"]	=	$typeOrder;
		
		if($_POST["frmEditID"]>0){
			
			$typeID = txtSec($_POST["frmEditID"]);			

			$db->where ('langID', txtSec($typeID));
			$db->update($db_lang, $post_value_arr);
			
		
		}
		else{
		
			$typeID = $db->insert($db_lang, $post_value_arr);
			
		}
		
		if($_FILES["frmIcon"]["tmp_name"]!=""){
			
								

			$fileName = $typeID.".jpg";			

			$to = $path.$fileName;
			
			if (file_exists($to))
			{
				unlink($to);
			}

			$from = $_FILES["frmIcon"]["tmp_name"];						

			if(move_uploaded_file($from,$to))	
				$proLogo = "";

		}
		
		
		$db->where("langID", $typeID, "!=");
		$db->where("langOrder", $typeOrder, ">=");
		$db->orderBy("langOrder","asc");
		$selTypeArr = $db->get($db_lang);
		
		$typeOrder += 1;
		
		if(count($selTypeArr)>0)
		foreach($selTypeArr as $key=>$obj){
			
			unset($post_value_arr);
			$post_value_arr["langOrder"]	=	$typeOrder;
			
			$db->where ('langID', $obj["langID"]);
			$db->update($db_lang, $post_value_arr);
			
			$typeOrder ++;
		
		}

		if($_POST["frmCopyLang"]>0){

			$copyLangID = $_POST["frmCopyLang"];
			$newLangID = $typeID;


			$cols = Array ("id", "color", "name", "pageSlide", "pageTitle", "icon", "menu_menu", "menuLoc", "pageType", "staticLink", "`order`");
			$db->where("lang", $copyLangID);
			$db->orderBy("id","asc");
			$menuArr = $db->get($tbl_main_menu,null,$cols);

			if(count($menuArr)>0)
			foreach($menuArr as $key=>$menuObj){

				unset($post_value_arr);
				$post_value_arr = $menuObj;
				$post_value_arr["lang"] = $newLangID;
				unset($post_value_arr['id']);
				if($menuObj["menu_menu"]>0)
					$post_value_arr["menu_menu"] = $newMenuIDArr[$menuObj["menu_menu"]];

				$newMenuID = $db->insert($tbl_main_menu, $post_value_arr);

				$newMenuIDArr[$menuObj["id"]] = $newMenuID;
			}


			$db->where("lang", $copyLangID);
			$db->orderBy("id","asc");
			$typeArr = $db->get($db_type);


			if(count($typeArr)>0)
			foreach($typeArr as $key=>$menuObj){

				unset($post_value_arr);
				$post_value_arr = $menuObj;
				$post_value_arr["lang"] = $newLangID;
				unset($post_value_arr['id']);
				if($menuObj["menu_menu"]>0)
					$post_value_arr["menu_menu"] = $newTypeIDArr[$menuObj["menu_menu"]];

				$newMenuID = $db->insert($db_type, $post_value_arr);

				$newTypeIDArr[$menuObj["id"]] = $newMenuID;
			}

			
			$schSql = "SELECT A.* FROM $db_ceo A WHERE A.`pageID` in (select id from $tbl_main_menu where lang = ?) ORDER BY A.`ceoID` ASC limit 0,1000";
			$ceoArr = $db->rawQuery($schSql, Array ($copyLangID));

			if(count($ceoArr)>0)
			foreach($ceoArr as $key=>$schObj){

				unset($post_value_arr);
				$post_value_arr = $schObj;
				unset($post_value_arr['ceoID']);
				$post_value_arr["lang"] = $newLangID;
				$post_value_arr["pageID"] = $newMenuIDArr[$schObj["pageID"]];

				if($schObj["proType"]!=""){
					
					$proTypeArr = explode("|",$schObj["proType"]);
					if(count($proTypeArr)>0){
						foreach($proTypeArr as $k=>$o){
							if($o!="")
								$proTypeNArr[$k] = $newTypeIDArr[$o];
						}
						$post_value_arr["proType"] = "|".join("|",$proTypeNArr)."|";
					}
				}

				if($schObj["ceoType"]!=""){
					
					$ceoTypeArr = explode("|",$schObj["ceoType"]);
					if(count($ceoTypeArr)>0){
						foreach($ceoTypeArr as $k=>$o){
							if($o!="")
								$ceoTypeNArr[$k] = $newTypeIDArr[$o];
						}
						$post_value_arr["ceoType"] = "|".join("|",$ceoTypeNArr)."|";
					}
				}

				if($schObj["ceoCat"]!=""){
					
					
					$post_value_arr["ceoCat"] = $newTypeIDArr[$schObj["ceoCat"]];
					
				}

				$newSchID = $db->insert($db_ceo, $post_value_arr);

				$ceoIDArr[$schObj["ceoID"]] = $schObj["ceoID"];
				$newDestIDArr[$schObj["ceoID"]] = $newSchID;
			}

		

			$schSql = "SELECT A.* FROM $db_pagesch A WHERE A.`schKey` in (select id from $tbl_main_menu where lang = ?) ORDER BY A.`schID` ASC limit 0,1000";
			$schArr = $db->rawQuery($schSql, Array($copyLangID));

			if(count($schArr)>0)
			foreach($schArr as $key=>$schObj){

				unset($post_value_arr);
				$post_value_arr = $schObj;
				unset($post_value_arr['schID']);
				$post_value_arr["schKey"] = $newMenuIDArr[$schObj["schKey"]];
				if($schObj["parentID"]>0)
					$post_value_arr["parentID"] = $newSchIDArr[$schObj["parentID"]];

				$newSchID = $db->insert($db_pagesch, $post_value_arr);

				$newSchIDArr[$schObj["schID"]] = $newSchID;
			}
			
			

		}
		
		
		
		$sysReturnLink =  "/settings/langs";
		
	
	break;
	case "langDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);
		
		
		$db->where('langID', $delID);
		$db->delete($db_lang);
		
		$sysReturnLink =  "/settings/langs";
		
	
	break;
}


?>