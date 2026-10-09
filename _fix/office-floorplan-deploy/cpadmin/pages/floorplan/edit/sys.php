<?php
/**
 * /floorplan/edit (цэсний холбоос) — cpadmin/.htaccess-д тусгай мөр нэмээгүй
 * үед ч ажиллана: ерөнхий дүрэм (^(.*)$ -> index.php?incPageType=$1) нь
 * "floorplan/edit" гэж дамжуулдаг тул index.php энэ файлыг ачаална.
 * .htaccess-д мөр нэмсэн бол ../sys.php шууд ажиллана — хоёулаа ижил.
 */

$incPage = "floorplan";            /* цэсэнд идэвхтэй харагдуулна */
$_REQUEST["subPage"] = "edit";

include __DIR__ . "/../sys.php";
