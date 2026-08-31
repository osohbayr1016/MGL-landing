<?php

$clkMenuMod = "visualisation";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


if(isset($_REQUEST["productID"])){
	
	
	include $clkMenuModDir."more.sys.php";
	$incPageUrl = $clkMenuModDir."more.php";
	
}

?>