<?php

$sysReturnLink =  "/info";

switch($_POST["frmPost"]){
	case "widgetPost":
		
		unset($post_value_arr);
		$post_value_arr["widgetTitle"]	=	txtSec($_POST["frmTitle"]);
		$post_value_arr["widgetSub"]	=	txtSec($_POST["frmIsSub"]);
		
		if($_POST["frmEditID"]>0){
		
			$widgetID = txtSec($_POST["frmEditID"]);

			$db->where ('id',$widgetID);
			$db->update($db_widgets, $post_value_arr);

		
		}
		else{

			$widgetID = $db->insert($db_widgets, $post_value_arr);

			
		}

		$db->where('widgetID', $widgetID);
		$db->delete($db_widget_cols);


		if(count($_POST["frmMainTitle"])>0)
		foreach($_POST["frmMainTitle"] as $key=>$obj){
			
			if($obj!=""){
				unset($post_value_arr);
				$post_value_arr["widgetID"]	=	$widgetID;
				$post_value_arr["colName"]	=	$obj;
				$post_value_arr["colKey"]	=	$_POST["frmMainKey"][$key];
				$post_value_arr["colType"]	=	$_POST["frmMainType"][$key];
				$post_value_arr["colOrder"]	=	$key;
				$post_value_arr["rowType"]	=	"main";				
				$db->insert($db_widget_cols, $post_value_arr);
				
				
			}
		
		}

		if(count($_POST["frmSubTitle"])>0)
		foreach($_POST["frmSubTitle"] as $key=>$obj){
			
			if($obj!=""){
				unset($post_value_arr);
				$post_value_arr["widgetID"]	=	$widgetID;
				$post_value_arr["colName"]	=	$obj;
				$post_value_arr["colKey"]	=	$_POST["frmSubKey"][$key];
				$post_value_arr["colType"]	=	$_POST["frmSubType"][$key];
				$post_value_arr["colOrder"]	=	$key;
				$post_value_arr["rowType"]	=	"sub";
				$db->insert($db_widget_cols, $post_value_arr);
				
			}
		
		}
		
		
		$sysReturnLink =  "/info/widget";
		
	
	break;
	case "typePost":
		
		$path = "postpic/type/";

		if(!(is_dir($path)))
			mkdir($path,0775);
		
	
		$typeOrder = txtSec($_POST["frmOrder"]);
		$mainType = txtSec($_POST["frmMainType"]);
		$mainCat = txtSec($_POST["frmMainCat"]);
		
		unset($post_value_arr);
		$post_value_arr["menu_menu"]	=	$mainCat;
		$post_value_arr["name"]			=	$_POST["frmName"];
		$post_value_arr["color"]		=	txtSec($_POST["frmColor"]);

		$post_value_arr["typeKeys"]		=	txtSec($_POST["frmKey"]);
		$post_value_arr["typeTitle"]	=	txtSec($_POST["frmTitle"]);
		$post_value_arr["type"]			=	$mainType;
		$post_value_arr["order"]		=	$typeOrder;
		
		if($_POST["frmEditID"]>0){
			
			$typeID = txtSec($_POST["frmEditID"]);
			

			$db->where ('id',$typeID);
			$db->update($db_type, $post_value_arr);
			
		
		}
		else{
			
			$post_value_arr["lang"]		=	$adminLang;
			
			$typeID = $db->insert($db_type, $post_value_arr);
			
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
		
		$db->where('type', $mainType);
		$db->where('menu_menu', $mainCat);
		$db->where('lang', $adminLang);
		$db->where('`order`', $typeOrder, ">=");
		$db->where('`id`', $typeID, "!=");
		$db->orderBy("`order`","asc");
		$selTypeArr = $db->get($db_type);
		
		$typeOrder += 1;
		
		if(count($selTypeArr)>0)
		foreach($selTypeArr as $key=>$obj){
			
			unset($post_value_arr);
			$post_value_arr["order"]	=	$typeOrder;
			$db->where ('id',$obj["id"]);
			$db->update($db_type, $post_value_arr);
			
			$typeOrder ++;
		
		}
		
		
		
		$sysReturnLink =  "/info/types/".$mainType."/0/#cat".$mainCat;
		
	
	break;
	case "menuPost":
		
		
	
		$typeOrder = txtSec($_POST["frmOrder"]);
		$mainType = txtSec($_POST["frmMainType"]);
		$mainCat = txtSec($_POST["frmMainMenu"]);
		
		unset($post_value_arr);
		$post_value_arr["menu_menu"]	=	$mainCat;
		$post_value_arr["name"]			=	txtSec($_POST["frmName"]);
		$post_value_arr["color"]		=	txtSec($_POST["frmColor"]);
		$post_value_arr["icon"]			=	txtSec($_POST["frmIcon"]);
		$post_value_arr["staticlink"]	=	txtSec($_POST["frmLinks"]);
		$post_value_arr["pageType"]		=	txtSec($_POST["frmType"]);
		$post_value_arr["menuLoc"]		=	$mainType;
		$post_value_arr["order"]		=	$typeOrder;
		
		if($_POST["frmEditID"]>0){
			
			$typeID = txtSec($_POST["frmEditID"]);
			
			
			$db->where ('id',$typeID);
			$db->update($tbl_main_menu, $post_value_arr);
			
		
		}
		else{
			
			$post_value_arr["lang"]		=	$adminLang;
			
			$typeID = $db->insert($tbl_main_menu, $post_value_arr);
			
		}
		
		
		$db->where('menuLoc', $mainType);
		$db->where('menu_menu', $mainCat);
		$db->where('lang', $adminLang);
		$db->where('`order`', $typeOrder, ">=");
		$db->where('`id`', $typeID, "!=");
		$db->orderBy("`order`","asc");
		$selTypeArr = $db->get($tbl_main_menu);
		
		$typeOrder += 1;
		
		if(count($selTypeArr)>0)
		foreach($selTypeArr as $key=>$obj){
			
			unset($post_value_arr);
			$post_value_arr["order"]	=	$typeOrder;
			$db->where ('id',$obj["id"]);
			$db->update($db_type, $post_value_arr);
			
			$typeOrder ++;
		
		}
		
		
		
		$sysReturnLink =  "/info/menus/".$mainType."/0/#cat".$mainCat;
		
	
	break;
	case "typeDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);
		
		
		$db->where('id', $delID);
		$db->orWhere ('menu_menu', $delID);
		$db->delete($db_type);

		
		$sysReturnLink =  "/info/types/".$_POST["frmMainType"]."/0";
		
	
	break;
	case "menuDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);
		

		$db->where('id', $delID);
		$db->orWhere ('menu_menu', $delID);
		$db->delete($tbl_main_menu);
		
		
		
		$sysReturnLink =  "/info/menus";
		
	
	break;
}


?>