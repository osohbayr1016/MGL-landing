<?php
/**
 * /openday/edit (цэсний холбоос) — cpadmin/.htaccess-д тусгай мөр нэмээгүй
 * ч ажиллана: ерөнхий дүрэм (^(.*)$ -> index.php?incPageType=$1) нь
 * "openday/edit" гэж дамжуулдаг тул index.php энэ файлыг ачаална.
 */

$incPage = "openday";              /* цэсэнд идэвхтэй харагдуулна */
$_REQUEST["subPage"] = "edit";

include __DIR__ . "/../sys.php";
