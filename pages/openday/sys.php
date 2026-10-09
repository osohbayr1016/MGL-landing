<?php
/**
 * Open Office Day — зочдын тур хуудас (/openday).
 *
 * Навигац, сайтын нийтлэг header ОРОХГҮЙ — зөвхөн шууд линк, QR-аар нээнэ
 * (noindex). Агуулга бүхэлдээ CP Admin -> "Өдөрлөг" хэсгээс удирдагдана.
 *
 * .htaccess-д мөр нэмэх ШААРДЛАГАГҮЙ: сайтын ерөнхий дүрэм
 * (^(.*)$ -> index.php?incPageType=$1) /openday-г энд авчирна.
 *
 * Схем нь Офис хуудасны давхрын схемийн зураг, viewer-ийг (assets/js/floorplan)
 * ашиглана. Цэгүүд нь Офис схемийнхээс ТУСДАА — зөвхөн өдөрлөгийнх.
 *
 * CP Admin -> Өдөрлөг нь ЯГ ЭНЭ файл + skin/new/openday.php-г "хуудсан дээр
 * засах" горимоор зурна (cpadmin/pages/openday/canvas.php). Тэр үед дуудахаас
 * өмнө тавина:
 *   $odEdit     = true
 *   $odData     — хадгалаагүй ноорог (OpenDayCore::load-той ижил бүтэц)
 *   $odAssetUrl — function($rel): CP Admin домэйн дээрх asset хаяг
 *   $odSiteBase — "https://mglenc.com" (бүртгэлийн дэвсгэр, логоны зам)
 */

include_once __DIR__ . "/../../class/openday.class.php";
include_once __DIR__ . "/../../class/floorplan.class.php";

$odEdit = !empty($odEdit);

/* ------------------------------------------------------------------
   Зочны асуулт (Асуулт хуудасны форм, fetch-ээр) — JSON буцаана.
   CP Admin -> Өдөрлөг -> Ирсэн асуулт хэсэгт шууд гарч ирнэ.
   ------------------------------------------------------------------ */

if (!$odEdit && isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST"
	&& isset($_POST["odAction"]) && $_POST["odAction"] === "ask") {

	$odOut = function ($status, $body) {
		if (!headers_sent()) {
			if ($status != 200) {
				header("HTTP/1.1 " . $status);
			}
			header("Content-Type: application/json; charset=utf-8");
			header("Cache-Control: no-store");
		}
		echo json_encode($body);
		exit;
	};

	/* robot: хүн харахгүй талбар дүүрсэн, эсвэл хуудас нээгээд 2 секунд болоогүй */
	$odHoney = isset($_POST["odWebsite"]) ? trim((string)$_POST["odWebsite"]) : "";
	$odTs    = isset($_POST["odTs"]) ? (int)$_POST["odTs"] : 0;
	if ($odHoney !== "" || ($odTs > 0 && time() - $odTs < 2)) {
		$odOut(200, array("ok" => 1));   /* robot-д амжилттай мэт харагдуулна */
	}

	if (!isset($db)) {
		$odOut(500, array("ok" => 0, "code" => "error"));
	}

	try {
		$odRes = OpenDayCore::askQuestion(
			$db,
			isset($_POST["question"]) ? $_POST["question"] : "",
			isset($_POST["name"]) ? $_POST["name"] : "",
			isset($_POST["lang"]) ? (string)$_POST["lang"] : "mn",
			isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ""
		);
	} catch (Exception $odErr) {
		error_log("openday ask failed: " . $odErr->getMessage());
		$odOut(500, array("ok" => 0, "code" => "error"));
	}

	$odOut($odRes["ok"] ? 200 : ($odRes["code"] === "rate" ? 429 : 422), array("ok" => $odRes["ok"] ? 1 : 0, "code" => $odRes["code"]));
}

/* ---- Аль хуудас: нүүр, хөтөлбөр, схем, мэдээлэл, асуулт ---- */

$odPages = array("home", "schedule", "map", "info", "faq");
if (!isset($odPage)) {
	$odPage = isset($_GET["p"]) && is_string($_GET["p"]) ? $_GET["p"] : "home";
	/* ?area=... — схемийн цэг рүү шууд */
	if (isset($_GET["area"]) && !isset($_GET["p"])) {
		$odPage = "map";
	}
}
if (!in_array($odPage, $odPages, true)) {
	$odPage = "home";
}

if (!isset($odData) || !is_array($odData)) {
	$odData = OpenDayCore::load(isset($db) ? $db : null, true);
}
$odSet   = $odData["settings"];
$odItems = $odData["items"];

/* засварлагч мөр бүрийг model дахь байрлалаар нь танина */
foreach ($odItems as $odK => $odList) {
	foreach ($odList as $odI => $odIt) {
		$odItems[$odK][$odI]["_i"] = $odI;
	}
}

if (!isset($odSiteBase)) {
	$odSiteBase = "";
}

if (!isset($odAssetUrl) || !is_callable($odAssetUrl)) {
	$odAssetUrl = function ($rel) {
		$f = rtrim(dirname(dirname(__DIR__)), "/\\") . "/" . ltrim($rel, "/");
		return "/" . ltrim($rel, "/") . (is_file($f) ? "?v=" . (int)filemtime($f) : "");
	};
}

/* ---- Давхрууд (зураг, хүрээ, шат) — Офис схемээс ---- */

$odFloors = array();
$odPlans  = FloorPlanCore::load(isset($db) ? $db : null, true);

foreach ($odPlans["floors"] as $odF) {
	$odP = $odF["plan"];
	$odNum = preg_match('/(\d+)/', $odP["key"], $odM) ? $odM[1] : $odP["key"];
	$odFloors[$odP["key"]] = array(
		"key"          => $odP["key"],
		"number"       => $odNum,
		/* хэлнээс хамааралгүй гарчиг — viewer давхар солиход өөрөө тавина */
		"floorTitle"   => $odNum . "F",
		"imageUrl"     => $odP["imageUrl"],
		"imageMidUrl"  => $odP["imageMidUrl"],
		"imageFullUrl" => $odP["imageFullUrl"],
		"imageWidth"   => $odP["imageWidth"],
		"imageHeight"  => $odP["imageHeight"],
		"bounds"       => $odP["bounds"],
		"link"         => $odP["link"],
		"hotspots"     => array()
	);
}

/* засах горимд схемийн зургийг CP Admin домэйнээс авна */
if ($odEdit) {
	foreach ($odFloors as $odKey => $odF) {
		foreach (array("imageUrl", "imageMidUrl", "imageFullUrl") as $odImg) {
			if ($odF[$odImg] !== "") {
				$odFloors[$odKey][$odImg] = $odAssetUrl("assets/images/floorplan/" . basename($odF[$odImg]));
			}
		}
	}
}

/* ---- Схемийн цэгүүд: зөвхөн байгаа давхар дээрх ---- */

$odSpots = array();      /* slug => item (хэвлэх, холбоос шалгахад) */
$odSpotJs = array();     /* slug => хоёр хэлтэй өгөгдөл (openday.js) */

foreach ($odItems["spot"] as $odI => $odS) {
	if (!isset($odFloors[$odS["floor"]])) {
		continue;
	}

	$odSpots[$odS["slug"]] = $odS;

	/* viewer-ийн бүтэц. Гарчиг/тайлбарыг монголоор өгнө; англи руу
	   шилжихэд openday.js хоёр хэлтэй $odSpotJs-ээс дахин тавина. */
	$odFloors[$odS["floor"]]["hotspots"][] = array(
		"slug"          => $odS["slug"],
		"order"         => $odI + 1,
		"enabled"       => $odEdit ? (bool)$odS["enabled"] : true,
		"kind"          => $odS["icon"] === "team" ? "team" : "other",
		"titleEn"       => $odS["titleMn"] !== "" ? $odS["titleMn"] : $odS["titleEn"],
		"titleMn"       => "",
		"descriptionMn" => $odS["bodyMn"] !== "" ? $odS["bodyMn"] : $odS["bodyEn"],
		"descriptionEn" => "",
		"x"             => $odS["x"],
		"y"             => $odS["y"],
		"desktop"       => array("zoom" => 2.8, "focusX" => null, "focusY" => null, "offsetX" => 0, "offsetY" => 0),
		"mobile"        => array("zoom" => 4.5, "focusX" => null, "focusY" => null, "offsetX" => 0, "offsetY" => 0)
	);

	$odSpotJs[$odS["slug"]] = array(
		"floor"   => $odS["floor"],
		"titleMn" => $odS["titleMn"],
		"titleEn" => $odS["titleEn"],
		"bodyMn"  => $odS["bodyMn"],
		"bodyEn"  => $odS["bodyEn"]
	);
}

/* 21 давхар шиг жижиг зурагтай давхарт ойртолтыг Офис схемийнхтэй ижил болгоно */
foreach ($odPlans["floors"] as $odF) {
	$odKey = $odF["plan"]["key"];
	if (!isset($odFloors[$odKey]) || (int)$odFloors[$odKey]["imageWidth"] >= 4000) {
		continue;
	}
	foreach ($odFloors[$odKey]["hotspots"] as $odHi => $odH) {
		$odFloors[$odKey]["hotspots"][$odHi]["desktop"]["zoom"] = 2.4;
		$odFloors[$odKey]["hotspots"][$odHi]["mobile"]["zoom"] = 2.8;
	}
}

$odFloorList = array_values($odFloors);

/* ---- Хөтөлбөр, маршрутын цаг (одоо / дараа нь) ---- */

$odAgendaJs = array();
foreach ($odItems["agenda"] as $odA) {
	$odAgendaJs[] = array("start" => $odA["start"], "end" => $odA["end"]);
}

$odActivityJs = array();
foreach ($odItems["activity"] as $odA) {
	$odActivityJs[] = array("start" => $odA["start"], "end" => $odA["end"]);
}

/* ---- Бүртгэлийн хуудастай ижил харагдац: дэвсгэр, өнгө, лого ----
   Зөвхөн уншина. Бүртгэлийн модуль байхгүй ч энэ хуудас ажиллана. */

$odLook = array("accent" => "#88aeb8", "bgPic" => "", "bgVideo" => "", "overlay" => 65, "pos" => "center", "logo" => "");

try {
	if (isset($db) && is_file(__DIR__ . "/../../class/registration.class.php")) {
		include_once __DIR__ . "/../../class/registration.class.php";

		$odRegT = RegistrationCore::tables();

		if (OpenDayCore::tableExists($db, $odRegT["setting"])) {
			$odRegSet = RegistrationCore::settings($db);

			if (preg_match('/^#[0-9a-fA-F]{6}$/', RegistrationCore::val($odRegSet, "themeAccent"))) {
				$odLook["accent"] = RegistrationCore::val($odRegSet, "themeAccent");
			}
			$odLook["bgPic"]   = trim(RegistrationCore::val($odRegSet, "pageBgPic"));
			$odLook["bgVideo"] = trim(RegistrationCore::val($odRegSet, "pageBgVideo"));
			$odLook["overlay"] = max(0, min(100, (int)RegistrationCore::val($odRegSet, "pageBgOverlay", "65")));
			$odPos = RegistrationCore::val($odRegSet, "pageBgPos", "center");
			$odLook["pos"] = in_array($odPos, array("center", "top", "bottom"), true) ? $odPos : "center";
		}

		if (OpenDayCore::tableExists($db, $odRegT["block"])) {
			$odRegRows = $db->rawQuery(
				"SELECT `blockData` FROM `" . $odRegT["block"] . "` WHERE `blockType`='hero' AND `blockStatus`=1"
					. " ORDER BY `blockOrder` ASC, `blockID` ASC",
				null
			);
			if (is_array($odRegRows)) {
				foreach ($odRegRows as $odRow) {
					$odD = RegistrationCore::decode($odRow["blockData"]);
					if (RegistrationCore::val($odD, "logo") != "") {
						$odLook["logo"] = RegistrationCore::val($odD, "logo");
						break;
					}
				}
			}
		}
	}
} catch (Exception $odErr) {
	/* харагдац л өөрчлөгдөнө — хуудас эвдрэхгүй */
} catch (Throwable $odErr) {
}

$odMedia = function ($path) use ($odSiteBase) {
	if ($path === "") {
		return "";
	}
	/* бүртгэлийн хуудастай ЯГ ижил замаар (skin/new/registration.php) */
	$url = function_exists("newsPicFnc") ? newsPicFnc(0, $path) : $path;

	/* CP Admin дээр засах үед: сайтын зам -> бүтэн хаяг */
	if ($odSiteBase !== "" && substr($url, 0, 1) === "/" && substr($url, 0, 2) !== "//") {
		$url = $odSiteBase . $url;
	}
	return $url;
};

$odLook["bgPicUrl"]   = $odMedia($odLook["bgPic"]);
$odLook["bgVideoUrl"] = $odMedia($odLook["bgVideo"]);
$odLook["logoUrl"]    = $odMedia($odLook["logo"]);

$gloIncHomePage = "openday.php";
