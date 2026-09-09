<?php
if($subMenuArr>0){
    $selWidDir  = "submenu";
    include $gloConstWidDir."widget.temp.php";
}
$selWidDir  = "pagesch";
include $gloConstWidDir . "widget.temp.php";

/* Өргөдлийн форм (pages/page/sys.php дээр шийдэгдсэн) */
if(isset($applyEmbed) && isset($applyEmbed[(int)$pageID])){
    include $gloConstModuleDir . "apply/form.php";
}
?>
