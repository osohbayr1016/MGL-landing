<?php include $incPageUrl; ?> 


<?php 
if(count($widJsArr)>0)
foreach($widJsArr as $key=>$incPageJS){
	$selWidID = txtSec($key);
	if($incPageJS!="" and is_file($incPageJS))
		include $incPageJS; 
}
?>