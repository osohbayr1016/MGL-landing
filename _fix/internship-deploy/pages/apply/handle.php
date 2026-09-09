<?php
/**
 * Өргөдлийн формын нийтлэг бэлтгэл ба илгээлтийг боловсруулах хэсэг.
 *
 * Хоёр газраас дуудагдана:
 *   1. pages/apply/sys.php   — /internship, /career хуудсууд
 *   2. pages/page/sys.php    — CP Admin дээр заасан /page/N хуудсууд
 *
 * Дуудахаас өмнө $applyType ("intern" | "job") утгыг өгсөн байх ёстой.
 */

include_once __DIR__ . "/../../class/apply.class.php";

ApplyCore::ensure($db);

$applyType   = ApplyCore::typeKey(isset($applyType) ? $applyType : "intern");
$applySet    = ApplyCore::settings($db);
$applyFields = ApplyCore::fields($db, $applyType, true);
$applyStatus = ApplyCore::status($applySet, $applyType);
$applyTbl    = ApplyCore::tables();

$applyErrors = array();
$applyValues = array();
$applyDone   = false;

/* Форм хаана буцаж POST хийх вэ (одоогийн хаяг) */
$applyPath   = strtok($_SERVER["REQUEST_URI"], "?");
$applyAction = $applyPath . "#apply-form";

/* Сайтын CSS-ийг нэмэлтээр ачаалуулах туг (skin/new/home.php) */
$applyCssOn = true;

/* PRG — амжилттай илгээсний дараа refresh хийхэд давхар бичихгүй */
if (!empty($_SESSION["applyDone"])) {
	$applyDone = true;
	unset($_SESSION["applyDone"]);
}

/* Хүсэлт post_max_size-аас хэтэрвэл PHP $_POST, $_FILES хоёрыг хоёуланг нь
   хаядаг. Тэр үед хэрэглэгчид ойлгомжтой алдаа харуулна. */
if ($_SERVER["REQUEST_METHOD"] == "POST" && count($_POST) < 1 && count($_FILES) < 1
	&& isset($_SERVER["CONTENT_LENGTH"]) && (int)$_SERVER["CONTENT_LENGTH"] > 0) {

	$applyErrors["_form"] = "Файл хэт том байна (" . ApplyCore::sizeText((int)$_SERVER["CONTENT_LENGTH"]) . "). "
		. "Энэ сервер дээр дээд тал нь " . ApplyCore::sizeText(ApplyCore::uploadMaxBytes()) . " багтана.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["frmApplyPost"])) {

	/* Илгээсэн формын төрлийг баримтална (нэг хуудсанд хоёр форм байж болно) */
	if (isset($_POST["applyType"])) {
		$applyType   = ApplyCore::typeKey($_POST["applyType"]);
		$applyFields = ApplyCore::fields($db, $applyType, true);
		$applyStatus = ApplyCore::status($applySet, $applyType);
	}

	/* 1. Robot хамгаалалт — хүн харахгүй талбар дүүрсэн бол хаяна */
	$applyHoney = isset($_POST["applyWebsite"]) ? trim($_POST["applyWebsite"]) : "";

	/* 2. Хэт хурдан илгээсэн бол (bot) */
	$applyOpened = isset($_POST["applyTs"]) ? (int)$_POST["applyTs"] : 0;
	$applyFast   = ($applyOpened > 0 && (time() - $applyOpened) < 2);

	if ($applyHoney != "" || $applyFast) {
		$applyErrors["_form"] = $applySet["errorText"];
	} elseif (!$applyStatus["open"]) {
		$applyErrors["_form"] = $applyStatus["text"];
	} else {

		/* 3. Нэг IP-ээс цагт 5-аас олон удаа илгээхийг хориглоно */
		$applyRecent = (int)ApplyCore::scalar($db,
			"SELECT COUNT(*) FROM `" . $applyTbl["entry"] . "` WHERE `entryIP`=?"
				. " AND `entryDate`>DATE_SUB(NOW(), INTERVAL 1 HOUR)",
			array(ApplyCore::clientIp())
		);

		if ($applyRecent >= 5) {
			$applyErrors["_form"] = $applySet["errorText"];
		} else {
			$applyCheck  = ApplyCore::validate($db, $applyFields, $_POST, $_FILES, $applySet);
			$applyValues = $applyCheck["values"];

			if ($applyCheck["ok"]) {
				ApplyCore::saveEntry($db, $applyType, $applyCheck["core"], $applyCheck["extra"], $applyCheck["files"]);

				$_SESSION["applyDone"] = 1;

				header("Location: " . $applyPath . "?ok=1#apply-form");
				exit;
			}

			$applyErrors = $applyCheck["errors"];
		}
	}
}
