<?php
/**
 * Арга хэмжээний бүртгэлийн хуудас — /registration
 *
 * Навигац, footer цэс, сайтын нийтлэг header ЮУ Ч ЭНД ОРОХГҮЙ.
 * Хуудсыг зөвхөн шууд линк эсвэл QR-аар нээнэ. Хайлтын системд
 * гаргахгүйн тулд noindex/nofollow тавьсан (skin/new/registration.php).
 *
 * Бүх агуулга, өнгө, зураг, блокийн дараалал, формын талбарууд
 * CP Admin -> "Арга хэмжээний бүртгэл" хэсгээс удирдагдана.
 */

include_once __DIR__ . "/../../class/registration.class.php";

RegistrationCore::ensure($db);

$regSet    = RegistrationCore::settings($db);
$regFields = RegistrationCore::fields($db, true);
$regStatus = RegistrationCore::status($db, $regSet);

$regErrors = array();
$regValues = array();
$regDone   = false;

/* PRG — амжилттай бүртгэлийн дараа refresh хийхэд давхар бичихгүй */
if (!empty($_SESSION["regDone"])) {
	$regDone = true;
	unset($_SESSION["regDone"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["frmRegPost"])) {

	/* 1. Robot хамгаалалт — хүн харахгүй талбар дүүрсэн бол хаяна */
	$honey = isset($_POST["regWebsite"]) ? trim($_POST["regWebsite"]) : "";

	/* 2. Хэт хурдан илгээсэн бол (bot) */
	$openedAt = isset($_POST["regTs"]) ? (int)$_POST["regTs"] : 0;
	$tooFast  = ($openedAt > 0 && (time() - $openedAt) < 2);

	if ($honey != "" || $tooFast) {
		$regErrors["_form"] = $regSet["errorText"];
	} elseif (!$regStatus["open"]) {
		$regErrors["_form"] = $regStatus["text"];
	} else {

		/* 3. Нэг IP-ээс цагт 10-аас олон удаа илгээхийг хориглоно */
		$regTbl = RegistrationCore::tables();
		$recent = (int)RegistrationCore::scalar($db, 
			"SELECT COUNT(*) FROM `" . $regTbl["entry"] . "` WHERE `entryIP`=? AND `entryDate`>DATE_SUB(NOW(), INTERVAL 1 HOUR)",
			array(RegistrationCore::clientIp())
		);

		if ($recent >= 10) {
			$regErrors["_form"] = $regSet["errorText"];
		} else {
			$check = RegistrationCore::validate($db, $regFields, $_POST, $regSet);
			$regValues = $check["values"];

			if ($check["ok"]) {
				RegistrationCore::saveEntry($db, $check["core"], $check["extra"]);

				$_SESSION["regDone"] = 1;
				header("Location: /registration?ok=1#registration-form");
				exit;
			}

			$regErrors = $check["errors"];
		}
	}
}

/* Бүртгэл хаагдсан/дүүрсэн эсэхийг илгээлтийн дараа дахин шалгана */
$regStatus = RegistrationCore::status($db, $regSet);
$regBlocks = RegistrationCore::blocks($db, true);

/* Формын блок байхгүй бол ч бүртгүүлэх боломжтой байх ёстой */
$regHasForm = false;
foreach ($regBlocks as $regBlockObj) {
	if ($regBlockObj["blockType"] == "form") {
		$regHasForm = true;
		break;
	}
}

/* Стандарт layout-ын оронд бие даасан хуудас */
$gloIncHomePage = "registration.php";
$allWidgetArr   = array();
$addPageTitle   = $regSet["metaTitle"];
