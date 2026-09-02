<?php
/**
 * Бүртгэлийн модулийн сангуудыг ачаална.
 *
 * Цөм код нь сайтын үндсэн хавтас дахь class/registration.class.php —
 * вэб сайт болон CP Admin хоёулаа ЯГ ижил файлыг ашигладаг тул
 * хүснэгтийн бүтэц, блок/талбарын каталог хэзээ ч зөрөхгүй.
 */

if (!class_exists("RegistrationCore")) {
	$regLibCandidates = array(
		__DIR__ . "/../../../class/registration.class.php",   /* public_html/class/ */
		__DIR__ . "/../../class/registration.class.php"       /* cpadmin/class/ (нөөц) */
	);

	foreach ($regLibCandidates as $regLibPath) {
		if (is_file($regLibPath)) {
			include_once $regLibPath;
			break;
		}
	}
}

if (!class_exists("XlsxWriter")) {
	$regXlsxCandidates = array(
		__DIR__ . "/../../../class/xlsx.writer.class.php",
		__DIR__ . "/../../class/xlsx.writer.class.php"
	);

	foreach ($regXlsxCandidates as $regXlsxPath) {
		if (is_file($regXlsxPath)) {
			include_once $regXlsxPath;
			break;
		}
	}
}

include_once __DIR__ . "/../insert/order.helper.php";

if (!class_exists("RegistrationCore")) {
	die('<div style="padding:40px;font:15px sans-serif;color:#a00">'
		. 'class/registration.class.php олдсонгүй. Файлыг сайтын үндсэн хавтасны class/ дотор байрлуулна уу.'
		. '</div>');
}

/* Хүснэгтүүд байхгүй бол энд үүсгэнэ (_sql/registration.sql-тэй ижил бүтэц).
   Нэг хүсэлтэд ганц удаа шалгана. */
if (empty($GLOBALS["regModuleReady"])) {
	RegistrationCore::ensure($db);
	$GLOBALS["regModuleReady"] = true;
}

/* Энэ мөр ЯМАГТ ажиллах ёстой — lib.php-г дахин оруулсан ч дэд файлууд
   $regTbl-гүй үлдэхгүй байх учиртай. */
$regTbl = RegistrationCore::tables();
