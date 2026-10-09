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
 */

include_once __DIR__ . "/../../class/openday.class.php";
include_once __DIR__ . "/../../class/floorplan.class.php";

$odData = OpenDayCore::load(isset($db) ? $db : null, true);
$odSet  = $odData["settings"];
$odItems = $odData["items"];

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
		"enabled"       => true,
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

$odMedia = function ($path) {
	if ($path === "") {
		return "";
	}
	/* бүртгэлийн хуудастай ЯГ ижил замаар (skin/new/registration.php) */
	return function_exists("newsPicFnc") ? newsPicFnc(0, $path) : $path;
};

$odLook["bgPicUrl"]   = $odMedia($odLook["bgPic"]);
$odLook["bgVideoUrl"] = $odMedia($odLook["bgVideo"]);
$odLook["logoUrl"]    = $odMedia($odLook["logo"]);

$gloIncHomePage = "openday.php";
