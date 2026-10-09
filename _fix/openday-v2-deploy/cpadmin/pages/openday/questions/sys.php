<?php
/**
 * /openday/questions (цэсний холбоос) — cpadmin/.htaccess-д тусгай мөр нэмээгүй
 * ч ажиллана: ерөнхий дүрэм (^(.*)$ -> index.php?incPageType=$1) нь
 * "openday/questions" гэж дамжуулдаг тул index.php энэ файлыг ачаална.
 */

$incPage = "openday";              /* цэсэнд идэвхтэй харагдуулна */
$_REQUEST["subPage"] = "questions";

include __DIR__ . "/../sys.php";
