<?php
$gloUserOnline = false;

if(isset($_SESSION["upass"]) && isset($_SESSION["umail"])) {
	
	$gloLang =1;
	$thisUserMail = txtSec($_SESSION["umail"]);
	$thisUserPas = txtSec($_SESSION["upass"]);
	
	$db->where ("aname", $thisUserMail);
	$db->where ("apass", $thisUserPas);
	$onlainUserObj = $db->getOne($tbl_prefadmin);

	
	if($onlainUserObj!=null){ 
	
		
		$onlainUserName		= $onlainUserObj["name"];
		$gloUserOnlineID	= $onlainUserObj["id"];
		
		$gloUserOnline		= true;
		
	
		$db->where ("adminGroupID", $onlainUserObj["adminGroupID"]);
		$adminGroupArr = $db->getOne($tbl_admingroup,"adminGroupAction");
		$adminAccessArr = explode("-",$adminGroupArr["adminGroupAction"]);
		
		
		if(count($adminAccessArr)>0)
		foreach($adminAccessArr as $key=>$obj){
			if($obj!=""){
				$objA = explode("_",$obj);
				$adminAccessPer[$objA[0]][$objA[1]]=$objA[1];
			}
		}

		if(isset($adminAccessPer["insert"]["promo"]) && !isset($adminAccessPer["insert"]["homeProjects"])){
			$adminAccessPer["insert"]["homeProjects"] = "homeProjects";
		}

		/* Арга хэмжээний бүртгэл (/registration).
		   const.php нь .gitignore-д байдаг тул цэсийг ЭНД бүртгэнэ —
		   ингэснээр серверт const.php-г гараар засахгүйгээр ажиллана. */
		if(!isset($gloMenuArr["registration"])){
			$gloMenuArr["registration"] = array(
				"label" => "Арга хэмжээний бүртгэл",
				"icon"  => "fa fa-check-square-o",
				"sub"   => array(
					"list"     => "Бүртгэлийн жагсаалт",
					"design"   => "Хуудасны дизайн",
					"fields"   => "Формын талбар",
					"settings" => "Тохиргоо"
				)
			);
		}

		/* Ямар нэг эрхтэй нэвтэрсэн админд автоматаар нээнэ.
		   (Эрхийн бүлэгт "registration_*" гараар нэмсэн бол түүнийг нь хэвээр үлдээнэ.
		    Хандах эрх -> Эрхийн бүлэг хэсгээс нарийвчлан тохируулж болно.) */
		if(count($adminAccessPer)>0 && !isset($adminAccessPer["registration"])){
			$adminAccessPer["registration"] = array(
				"list"     => "list",
				"design"   => "design",
				"fields"   => "fields",
				"settings" => "settings"
			);
		}

		if(isset($gloMenuArr["insert"]["sub"]) && !isset($gloMenuArr["insert"]["sub"]["homeProjects"])){
			$hpSub = array();
			foreach($gloMenuArr["insert"]["sub"] as $hpKey=>$hpLabel){
				$hpSub[$hpKey] = $hpLabel;
				if($hpKey === "promo"){
					$hpSub["homeProjects"] = "Нүүр хуудасны төслүүд";
				}
			}
			if(!isset($hpSub["homeProjects"])){
				$hpSub["homeProjects"] = "Нүүр хуудасны төслүүд";
			}
			$gloMenuArr["insert"]["sub"] = $hpSub;
		}
		
		$db->orderBy("langOrder","asc");
		$sysLangArr = $db->get($db_lang);

		
		if(!$_SESSION["adminLang"])
			$_SESSION["adminLang"] = $sysLangArr[0]["langID"];
			
		$adminLang =  $_SESSION["adminLang"];

		foreach($sysLangArr as $key=>$obj){
			if($obj["langID"]==$adminLang)
				$selAdminLang = $obj;
		}
	}
	
}
	
?>