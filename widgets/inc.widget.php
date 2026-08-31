<?php 
//echo "<pre>".print_r($allWidgetArr,true);
if(isset($allWidgetArr))
foreach($allWidgetArr as $key=>$selWidDir){
	
	
	if(is_file("widgets/".$selWidDir."/sys.php")){
		include "widgets/".$selWidDir."/sys.php";
	}
	
}
?>