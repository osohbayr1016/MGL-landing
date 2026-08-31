<?php
$kkkkk = 0;

	
if(count($allSchArr)>0){
foreach($allSchArr as $keys=>$objs){
	


	$selSchBody = json_decode($objs["schNote"],true);

	$incTemp = "wid".$objs["schTemp"].".php";
	if(!is_file("widgets/pagesch/".$incTemp))
		$incTemp = "def.php";


	include $incTemp;

	

	$kkkkk++;
}  
}
else
	include "widgets/nocontent/temp.php";
?>	