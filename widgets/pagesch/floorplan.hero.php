<?php
/**
 * Офис хуудасны (about) hero: интерактив давхрын схем (20, 21-р давхар).
 *
 * widgets/pagesch/temp.php нь эхний section-ыг (Профайл) буфер рүү хийж,
 * энэ файлыг дуудна. Оролт:
 *   $fpWidHtml  — эхний section-ы бэлэн HTML
 *   $fpData     — FloorPlanCore::load(..., true) үр дүн (идэвхтэй давхар, цэгүүд)
 * Гаралт:
 *   $fpFinalHtml — hero-тэй нэгтгэсэн HTML
 *
 * Хуучин "pageHeader" баннер (зураг + h1) олдвол ЯГ ТЭРИЙГ hero-оор солино
 * (anchor id, h1 хоёр хадгалагдана). Олдохгүй бол hero-г section-ы өмнө
 * нэмнэ — хуудас хэзээ ч эвдрэхгүй.
 */

$fpFloors = $fpData["floors"];

$fpBase = rtrim(dirname(dirname(__DIR__)), "/\\") . "/";

$fpVer = function ($rel) use ($fpBase) {
	$f = $fpBase . ltrim($rel, "/");
	return is_file($f) ? (int)filemtime($f) : 1;
};

$fpAnchor = "fp-hero";
$fpH1     = "Профайл";
$fpPattern = '~<div class="wrapper">\s*<div class="pageHeader" id="(widhas\d+)">(.*?)</picture>\s*</div>~s';

$fpReplaced = false;

if (preg_match($fpPattern, $fpWidHtml, $fpM)) {
	$fpAnchor = $fpM[1];
	if (preg_match('~<h1[^>]*>(.*?)</h1>~s', $fpM[2], $fpH1M)) {
		$fpT = trim(strip_tags($fpH1M[1]));
		if ($fpT !== "") {
			$fpH1 = $fpT;
		}
	}
	$fpReplaced = true;
}

/* Давхрын дугаар (office-21f -> "21") — ?floor=21, шатны тэмдэг дээр харагдана */
$fpNumber = function ($key) {
	return preg_match('/(\d+)/', $key, $m) ? $m[1] : $key;
};

$fpJsonFloors = array();
foreach ($fpFloors as $fpF) {
	$fpP = $fpF["plan"];
	$fpJsonFloors[] = array(
		"key"          => $fpP["key"],
		"number"       => $fpNumber($fpP["key"]),
		"floorTitle"   => $fpP["floorTitle"],
		"imageUrl"     => $fpP["imageUrl"],
		"imageMidUrl"  => $fpP["imageMidUrl"],
		"imageFullUrl" => $fpP["imageFullUrl"],
		"imageWidth"   => $fpP["imageWidth"],
		"imageHeight"  => $fpP["imageHeight"],
		"bounds"       => $fpP["bounds"],
		"link"         => $fpP["link"],
		"hotspots"     => $fpF["hotspots"]
	);
}

$fpFirst = $fpFloors[0]["plan"];

/* сайтын англи хувилбар дээр англи тайлбар (байхгүй бол монгол) */
$fpLang = (isset($gloLangObj["langKey"]) && strtolower($gloLangObj["langKey"]) === "en") ? "en" : "mn";
$fpDesc = function ($s) use ($fpLang) {
	if ($fpLang === "en") {
		return $s["descriptionEn"] !== "" ? $s["descriptionEn"] : $s["descriptionMn"];
	}
	return $s["descriptionMn"] !== "" ? $s["descriptionMn"] : $s["descriptionEn"];
};

ob_start();
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&amp;display=swap">
<link rel="stylesheet" href="/assets/css/floorplan.css?v=<?php echo $fpVer("assets/css/floorplan.css");?>">
<noscript><style>.fp-floor.is-active .fp-world{opacity:1}</style></noscript>
<section class="fp-hero" id="<?php echo FloorPlanCore::esc($fpAnchor);?>" data-fp-root aria-label="Оффисын давхрын схем / Office floor plan">
	<h1 class="fp-sr-only"><?php echo FloorPlanCore::esc($fpH1);?></h1>
	<div class="fp-stage" data-fp-stage>
		<div class="fp-floors">
<?php foreach ($fpFloors as $fpI => $fpF) { $fpP = $fpF["plan"]; ?>
			<div class="fp-floor<?php echo $fpI === 0 ? " is-active" : "";?>" data-fp-floor="<?php echo FloorPlanCore::esc($fpP["key"]);?>" data-phase="OVERVIEW"<?php echo $fpI === 0 ? "" : ' aria-hidden="true"';?>>
				<div class="fp-world" data-fp-world>
					<img class="fp-image" <?php echo $fpI === 0 ? "src" : "data-src";?>="<?php echo FloorPlanCore::esc($fpP["imageUrl"]);?>"
						width="<?php echo (int)$fpP["imageWidth"];?>" height="<?php echo (int)$fpP["imageHeight"];?>"
						alt="<?php echo FloorPlanCore::esc($fpP["floorTitle"]);?>" decoding="async"<?php echo $fpI === 0 ? ' fetchpriority="high"' : "";?> draggable="false">
				</div>
				<div class="fp-spots" data-fp-spots></div>
				<p class="fp-fail" role="status">Схемийг ачаалж чадсангүй. Хуудсаа дахин ачаална уу.</p>
			</div>
<?php } ?>
		</div>

		<div class="fp-title">
			<button type="button" class="fp-title-btn" data-fp-floor-switch<?php echo count($fpFloors) < 2 ? " disabled" : "";?>>
				<span class="fp-title-text" data-fp-title><?php echo FloorPlanCore::esc($fpFirst["floorTitle"]);?></span>
				<span class="fp-title-go" data-fp-title-go aria-hidden="true"<?php echo count($fpFloors) < 2 ? " hidden" : "";?>>
					<svg viewBox="0 0 24 24"><path d="M12 19V5M6 11l6-6 6 6"/></svg><span data-fp-title-next></span>
				</span>
			</button>
		</div>

		<div class="fp-info" data-fp-info aria-live="polite" aria-atomic="true">
			<div class="fp-info-inner">
				<h2 class="fp-info-en" data-fp-en></h2>
				<p class="fp-info-mn" data-fp-mn></p>
				<p class="fp-info-desc" data-fp-desc></p>
			</div>
		</div>

		<div class="fp-nav" role="group" aria-label="Өрөө сонгох / Browse areas">
			<button type="button" class="fp-btn" data-fp-prev aria-label="Өмнөх / Previous">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
			</button>
			<button type="button" class="fp-btn fp-btn-down" data-fp-back aria-label="Бүх схем рүү буцах / Back to overview">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg>
			</button>
			<button type="button" class="fp-btn" data-fp-next aria-label="Дараах / Next">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
			</button>
		</div>

		<p class="fp-hint" data-fp-hint aria-hidden="true">
			<svg viewBox="0 0 24 24"><path d="M9 11V5.5a1.5 1.5 0 0 1 3 0V11m0-1.5V4a1.5 1.5 0 0 1 3 0v6m0-1.5a1.5 1.5 0 0 1 3 0V14a6 6 0 0 1-6 6h-1.2a6 6 0 0 1-4.6-2.2L4 15a1.6 1.6 0 0 1 2.4-2.1L9 15"/></svg>
			<span data-fp-hint-text>Чирж хөдөлгөх · Гүйлгэж томруулах</span>
		</p>

		<p class="fp-sr-only" data-fp-announce aria-live="polite"></p>
	</div>

	<div class="fp-sr-only" data-fp-fallback>
<?php foreach ($fpFloors as $fpF) { ?>
		<h2><?php echo FloorPlanCore::esc($fpF["plan"]["floorTitle"]);?></h2>
		<ul>
<?php foreach ($fpF["hotspots"] as $fpS) { ?>
			<li><?php echo FloorPlanCore::esc($fpS["titleEn"]);?> — <?php echo FloorPlanCore::esc($fpS["titleMn"]);?><?php if ($fpDesc($fpS) !== "") { echo ": " . FloorPlanCore::esc($fpDesc($fpS)); }?></li>
<?php } ?>
		</ul>
<?php } ?>
	</div>

	<script type="application/json" data-fp-data><?php echo FloorPlanCore::jsonForHtml(array("lang" => $fpLang, "floors" => $fpJsonFloors));?></script>
</section>
<script src="/assets/js/floorplan/core.js?v=<?php echo $fpVer("assets/js/floorplan/core.js");?>" defer></script>
<script src="/assets/js/floorplan/viewer.js?v=<?php echo $fpVer("assets/js/floorplan/viewer.js");?>" defer></script>
<script src="/assets/js/floorplan/public.js?v=<?php echo $fpVer("assets/js/floorplan/public.js");?>" defer></script>
<?php
$fpHeroHtml = ob_get_clean();

if ($fpReplaced) {
	/* callback: JSON доторх "\u" / "$" тэмдэгтийг regex-ийн орлуулалт гэж ойлгохгүй */
	$fpFinalHtml = preg_replace_callback($fpPattern, function ($m) use ($fpHeroHtml) {
		return $fpHeroHtml . '<div class="wrapper">';
	}, $fpWidHtml, 1);
} else {
	$fpFinalHtml = $fpHeroHtml . $fpWidHtml;
}
