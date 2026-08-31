<?php 
if($subMenuArr>0){
    $selWidDir  = "submenu";
    include $gloConstWidDir."widget.temp.php";
}
else{
    $selWidDir  = "widmenu";
    include $gloConstWidDir."widget.temp.php";
}
?>
<?php 
$selWidDir  = "pagesch";
include $gloConstWidDir."widget.temp.php";
?>