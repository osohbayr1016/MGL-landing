<?php
   
include_once("const.php");
if (is_file(__DIR__ . "/const.local.php")) {
	include_once __DIR__ . "/const.local.php";
}
include_once "class/class.config.php";
include_once "config.db.php";
include_once "functions.php";
include_once __DIR__ . "/r2.php";
?>