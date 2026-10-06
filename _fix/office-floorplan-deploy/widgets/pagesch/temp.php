<?php
$kkkkk = 0;

/* Офис (about) хуудасны hero — интерактив давхрын схем.
   Ямар нэг алдаа гарвал (хүснэгт, файл дутуу г.м.) хуучин баннер хэвээр
   гарна: хуудас хэзээ ч унахгүй. */
$fpData = null;
if(isset($clkMenuMod) && $clkMenuMod=="about" && count($allSchArr)>0){
	try{
		$fpClass = dirname(dirname(__DIR__))."/class/floorplan.class.php";
		if(is_file($fpClass)){
			include_once $fpClass;
			$fpLoaded = FloorPlanCore::load(isset($db) ? $db : null, true);
			/* идэвхтэй, дор хаяж нэг идэвхтэй цэгтэй давхар байвал л hero гарна */
			$fpHasSpots = false;
			foreach($fpLoaded["floors"] as $fpFloor){
				if(count($fpFloor["hotspots"])>0)
					$fpHasSpots = true;
			}
			if($fpHasSpots)
				$fpData = $fpLoaded;
		}
	}
	catch(Throwable $fpErr){
		$fpData = null;
	}
}


if(count($allSchArr)>0){
foreach($allSchArr as $keys=>$objs){



	$selSchBody = json_decode($objs["schNote"],true);

	$incTemp = "wid".$objs["schTemp"].".php";
	if(!is_file("widgets/pagesch/".$incTemp))
		$incTemp = "def.php";


	if($kkkkk===0 && $fpData!==null){
		$fpLevel = ob_get_level();
		ob_start();
		include $incTemp;
		$fpWidHtml = ob_get_clean();

		try{
			include __DIR__."/floorplan.hero.php";
			echo $fpFinalHtml;
		}
		catch(Throwable $fpErr){
			while(ob_get_level() > $fpLevel)
				ob_end_clean();
			echo $fpWidHtml;
		}
	}
	else
		include $incTemp;



	$kkkkk++;
}
}
else
	include "widgets/nocontent/temp.php";
?>