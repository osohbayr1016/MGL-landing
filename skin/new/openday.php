<?php
/**
 * Open Office Day — зочдын тур хуудасны layout (/openday).
 *
 * Сайтын header/footer/навигацийг ЗОРИУДААР оруулаагүй — зөвхөн шууд линк,
 * QR-аар нээгдэнэ. Харагдац нь бүртгэлийн хуудастай (/registration) ижил:
 * тэр хуудасны дэвсгэр зураг, өнгийг уншиж ашиглана (pages/openday/sys.php).
 *
 * Хуудаснууд: нүүр (/openday), хөтөлбөр (?p=schedule), схем (?p=map),
 * мэдээлэл (?p=info), асуулт (?p=faq). Бүгд нэг HTML-д байх ба нэг удаад
 * зөвхөн нэг нь харагдана — сервер ?p=-ээр зөвийг нь нээж, цэсний товч
 * дарахад assets/js/openday.js хуудсыг дахин ачаалахгүйгээр солино.
 *
 * Хоёр хэл: текст бүр монгол + англи хоёуланг нь гаргаж (OpenDayCore::bi),
 * <html data-lang> -аар нэгийг нь CSS нууна.
 *
 * $odEdit үнэн бол (CP Admin -> Өдөрлөг) ЯГ ЭНЭ хуудас "хуудсан дээр засах"
 * горимоор зурагдана: талбар бүр data-ode-f замтай, хоосон хэсэг ч харагдана,
 * мөр бүр data-ode-row-той. Засах багажийг cpadmin/pages/openday/canvas.js нэмнэ.
 */

$odT = function ($mn, $en) {
	return OpenDayCore::bi($mn, $en);
};

/** Засах боломжтой хоёр хэлтэй текст ($odEdit=false бол bi()-тэй ижил). */
$odF = function ($base, $mn, $en, $multi = false, $ph = "") use ($odEdit) {
	return OpenDayCore::field($odEdit, $base, $mn, $en, $multi, $ph);
};

/** Засах горимын мөрийн шинж: data-ode-row + нуусан мөрийн класс. */
$odRow = function ($kind, $it) use ($odEdit) {
	return $odEdit ? ' data-ode-row="' . $kind . '" data-ode-i="' . (int)$it["_i"] . '"' : "";
};
$odOff = function ($it) use ($odEdit) {
	return $odEdit && !$it["enabled"] ? " od-off" : "";
};

/** Засах горимын "нэмэх" товч. */
$odAdd = function ($kind, $mn) use ($odEdit) {
	return $odEdit ? '<button type="button" class="ode-add" data-ode-add="' . $kind . '"><i class="fa fa-plus" aria-hidden="true"></i> ' . OpenDayCore::esc($mn) . '</button>' : "";
};

/** Хуудас (view): одоогийнх биш бол нуугдана. */
$odView = function ($name) use ($odPage) {
	return ' data-od-view="' . $name . '"' . ($odPage === $name ? "" : " hidden");
};

/* "2026-10-17" -> "2026.10.17" */
$odDateText = $odSet["eventDate"] !== "" ? str_replace("-", ".", $odSet["eventDate"]) : "";
$odTimeText = OpenDayCore::timeRange($odSet["startTime"], $odSet["endTime"]);

/** Байршлын товч: тухайн цэгийг схем дээр нээнэ. Засах горимд — байршил сонгогч. */
$odWhere = function ($slug, $kind = "", $it = null) use ($odSpots, $odFloors, $odEdit) {
	$has = $slug !== "" && isset($odSpots[$slug]);
	$path = $odEdit ? $kind . "." . (int)$it["_i"] . ".spot" : "";

	if ($odEdit && !$has) {
		return '<button type="button" class="od-where ode-empty-pick" data-ode-pick="spot" data-ode-path="' . $path . '">'
			. '<i class="fa fa-map-marker" aria-hidden="true"></i><span class="od-where-name">+ Байршил</span></button>';
	}
	if (!$has) {
		return "";
	}

	$s = $odSpots[$slug];
	$num = isset($odFloors[$s["floor"]]) ? $odFloors[$s["floor"]]["number"] . "F" : "";

	return '<button type="button" class="od-where"'
		. ($odEdit ? ' data-ode-pick="spot" data-ode-path="' . $path . '"' : ' data-od-spot="' . OpenDayCore::esc($slug) . '"') . '>'
		. '<i class="fa fa-map-marker" aria-hidden="true"></i>'
		. '<span class="od-where-name">' . OpenDayCore::bi($s["titleMn"], $s["titleEn"]) . '</span>'
		. ($num !== "" ? '<span class="od-where-floor">' . OpenDayCore::esc($num) . '</span>' : "")
		. '</button>';
};

/** Засах горимд цагийн талбар, бусад үед энгийн текст. */
$odTime = function ($path, $value, $ph = "--:--") use ($odEdit) {
	if (!$odEdit) {
		return OpenDayCore::esc($value);
	}
	return '<span data-ode-f="' . $path . '" data-ode-type="time" data-ph="' . $ph . '">' . OpenDayCore::esc($value) . '</span>';
};

$odHas = function ($a, $b) use ($odEdit) {
	return $odEdit || $a !== "" || $b !== "";
};

$odHasMap = count($odFloorList) > 0 && (count($odSpots) > 0 || $odEdit);
$odAskOn  = (string)$odSet["askOn"] !== "0";

/* хуудаснууд: түлхүүр, icon, гарчиг (тохиргооноос — нүүрний товчлуурт), харагдах эсэх, цэсний богино нэр */
$odPageDefs = array(
	array("schedule", "fa-clock-o",         "schedTitle", $odEdit || count($odItems["agenda"]) > 0,          "Хөтөлбөр", "Programme"),
	array("map",      "fa-map-marker",      "mapTitle",   $odHasMap,                                         "Схем",     "Map"),
	array("info",     "fa-info-circle",     "infoTitle",  $odEdit || count($odItems["info"]) > 0,            "Мэдээлэл", "Info"),
	array("faq",      "fa-question-circle", "faqTitle",   $odEdit || count($odItems["faq"]) > 0 || $odAskOn, "Асуулт",   "Q&A")
);
$odNav = array();
foreach ($odPageDefs as $odD) {
	if ($odD[3]) {
		$odNav[] = $odD;
	}
}

$odTitlePlain = $odSet["titleMn"] !== "" ? $odSet["titleMn"] : $odSet["titleEn"];

$odBgStyle = "";
if ($odLook["bgPicUrl"] !== "") {
	$odBgStyle .= "background-image:url('" . OpenDayCore::esc($odLook["bgPicUrl"]) . "');";
}
$odBgStyle .= "background-position:" . OpenDayCore::esc($odLook["pos"]) . ";";

/* openday.js-д: цаг, цэгийн хоёр хэлтэй нэр/тайлбар */
$odJs = array(
	"date"   => $odSet["eventDate"],
	"start"  => $odSet["startTime"],
	"end"    => $odSet["endTime"],
	"agenda" => $odAgendaJs,
	"spots"  => $odSpotJs
);
?><!DOCTYPE html>
<html lang="mn" data-lang="mn">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />

<!-- Хайлтын систем болон нийгмийн сүлжээнд гаргахгүй -->
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet" />
<meta name="googlebot" content="noindex, nofollow" />
<meta name="theme-color" content="#0E0E0E" />

<title><?php echo OpenDayCore::esc($odTitlePlain); ?> | MGL E&amp;C</title>

<?php if (!$odEdit) { ?>
<script>
/* Хэлийг зурахаас ӨМНӨ тавина — монгол текст анивчихгүй */
(function () {
	try {
		var q = /[?&]lang=(en|mn)\b/.exec(location.search);
		var l = q ? q[1] : localStorage.getItem("od-lang");
		if (l === "en") {
			document.documentElement.setAttribute("data-lang", "en");
			document.documentElement.lang = "en";
		}
	} catch (e) { /* хувийн цонх: монголоор үлдэнэ */ }
})();
</script>
<?php } ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&amp;family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&amp;display=swap" rel="stylesheet">
<link href="<?php echo OpenDayCore::esc($odEdit ? $odAssetUrl("assets/client/plugins/font-awesome/css/font-awesome.css") : "/assets/client/plugins/font-awesome/css/font-awesome.css"); ?>" rel="stylesheet">
<?php if ($odHasMap) { ?>
<link href="<?php echo OpenDayCore::esc($odAssetUrl("assets/css/floorplan.css")); ?>" rel="stylesheet">
<?php } ?>
<link href="<?php echo OpenDayCore::esc($odAssetUrl("assets/css/openday.css")); ?>" rel="stylesheet">
<?php if ($odEdit) { ?>
<link href="<?php echo OpenDayCore::esc($odAssetUrl("canvas.css")); ?>" rel="stylesheet">
<?php } ?>
<style>:root{--od-accent:<?php echo OpenDayCore::esc($odLook["accent"]); ?>;}</style>
</head>
<body class="od-body<?php if ($odLook["bgPicUrl"] !== "" || $odLook["bgVideoUrl"] !== "") echo " od-has-bg"; ?><?php if ($odEdit) echo " od-editing"; ?>" data-od-page="<?php echo $odPage; ?>">

<div class="od-bg" aria-hidden="true">
	<div class="od-bg-media" style="<?php echo $odBgStyle; ?>">
		<?php if ($odLook["bgVideoUrl"] !== "") { ?>
		<video class="od-bg-video" autoplay muted loop playsinline preload="metadata" src="<?php echo OpenDayCore::esc($odLook["bgVideoUrl"]); ?>"></video>
		<?php } ?>
	</div>
	<span class="od-bg-shade" style="opacity:<?php echo max(0.55, $odLook["overlay"] / 100); ?>"></span>
</div>

<header class="od-top" data-od-top>
	<div class="od-top-in">
		<a class="od-brand" href="?p=home" data-od-go="home" aria-label="<?php echo OpenDayCore::esc($odTitlePlain); ?>">
			<?php if ($odLook["logoUrl"] !== "") { ?>
			<img src="<?php echo OpenDayCore::esc($odLook["logoUrl"]); ?>" alt="MGL E&amp;C" height="28">
			<?php } else { ?>
			<span class="od-brand-text">MGL E&amp;C</span>
			<?php } ?>
		</a>
		<?php if (count($odNav) > 0) { ?>
		<nav class="od-tabs" aria-label="<?php echo OpenDayCore::esc("Хуудаснууд / Pages"); ?>">
			<?php foreach ($odNav as $odN) { ?>
			<a href="?p=<?php echo $odN[0]; ?>" data-od-go="<?php echo $odN[0]; ?>"<?php if ($odPage === $odN[0]) echo ' class="is-active" aria-current="page"'; ?>><?php echo $odT($odN[4], $odN[5]); ?></a>
			<?php } ?>
		</nav>
		<?php } ?>
		<button type="button" class="od-lang" data-od-lang aria-label="Switch language / Хэл солих">
			<span class="od-mn">EN</span><span class="od-en">MN</span>
		</button>
	</div>
</header>

<main class="od-page" id="top">

	<!-- ================= Нүүр ================= -->
	<section class="od-view od-hero"<?php echo $odView("home"); ?>>
		<div class="od-wrap">
			<?php if ($odHas($odSet["eyebrowMn"], $odSet["eyebrowEn"])) { ?>
			<p class="od-eyebrow"><?php echo $odF("s.eyebrow", $odSet["eyebrowMn"], $odSet["eyebrowEn"], false, "Дээд жижиг текст"); ?></p>
			<?php } ?>
			<h1 class="od-title"><?php echo $odF("s.title", $odSet["titleMn"], $odSet["titleEn"], false, "Гарчиг"); ?></h1>

			<ul class="od-meta">
				<?php if ($odDateText !== "" || $odEdit) { ?>
				<li<?php if ($odEdit) echo ' data-ode-pick="date" title="Огноо солих"'; ?>><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo OpenDayCore::esc($odDateText !== "" ? $odDateText : "Огноо"); ?></li>
				<?php } ?>
				<?php if ($odTimeText !== "" || $odEdit) { ?>
				<li<?php if ($odEdit) echo ' data-ode-pick="hours" title="Цаг солих"'; ?>><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo OpenDayCore::esc($odTimeText !== "" ? $odTimeText : "Цаг"); ?></li>
				<?php } ?>
				<?php if ($odHas($odSet["locationMn"], $odSet["locationEn"])) { ?>
				<li><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $odF("s.location", $odSet["locationMn"], $odSet["locationEn"], false, "Байршил"); ?></li>
				<?php } ?>
			</ul>

			<?php if ($odHas($odSet["introMn"], $odSet["introEn"])) { ?>
			<p class="od-intro"><?php echo $odF("s.intro", $odSet["introMn"], $odSet["introEn"], true, "Мэндчилгээ, танилцуулга"); ?></p>
			<?php } ?>

			<?php if (count($odItems["agenda"]) > 0 && !$odEdit) { ?>
			<!-- Одоо / дараа нь — өдөрлөгийн өдөр openday.js дүүргэнэ -->
			<div class="od-live" data-od-live hidden>
				<p class="od-live-msg" data-od-live-msg></p>
				<div class="od-live-row is-now" data-od-live-now hidden>
					<span class="od-live-tag"><span class="od-dot" aria-hidden="true"></span><?php echo $odT("Одоо", "Now"); ?></span>
					<div class="od-live-body" data-od-live-now-body></div>
				</div>
				<div class="od-live-row is-next" data-od-live-next hidden>
					<span class="od-live-tag"><?php echo $odT("Дараа нь", "Up next"); ?></span>
					<div class="od-live-body" data-od-live-next-body></div>
				</div>
			</div>
			<?php } ?>
			<?php if ($odEdit) { ?>
			<p class="ode-note"><i class="fa fa-info-circle" aria-hidden="true"></i> Өдөрлөгийн өдөр энд "ОДОО / ДАРАА НЬ" хэсэг хөтөлбөрөөс автоматаар гарна.</p>
			<?php } ?>

			<?php if (count($odNav) > 0) { ?>
			<nav class="od-tiles" aria-label="<?php echo OpenDayCore::esc("Хуудаснууд / Pages"); ?>">
				<?php foreach ($odNav as $odN) { ?>
				<a class="od-tile" href="?p=<?php echo $odN[0]; ?>" data-od-go="<?php echo $odN[0]; ?>">
					<i class="fa <?php echo $odN[1]; ?> od-tile-icon" aria-hidden="true"></i>
					<span class="od-tile-name"><?php echo $odT($odSet[$odN[2] . "Mn"], $odSet[$odN[2] . "En"]); ?></span>
					<i class="fa fa-angle-right od-tile-go" aria-hidden="true"></i>
				</a>
				<?php } ?>
			</nav>
			<?php } ?>
		</div>
	</section>

	<!-- ================= Хөтөлбөр ================= -->
	<?php if ($odEdit || count($odItems["agenda"]) > 0) { ?>
	<section class="od-view od-section" id="schedule" aria-labelledby="od-h-schedule"<?php echo $odView("schedule"); ?>>
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-schedule"><?php echo $odF("s.schedTitle", $odSet["schedTitleMn"], $odSet["schedTitleEn"], false, "Хэсгийн гарчиг"); ?></h2>
			<ol class="od-agenda" data-od-agenda>
				<?php foreach ($odItems["agenda"] as $odI => $odA) { $odP = "agenda." . $odA["_i"]; ?>
				<li class="od-ag<?php echo $odOff($odA); ?>" data-od-i="<?php echo $odI; ?>"<?php echo $odRow("agenda", $odA); ?>>
					<div class="od-ag-time">
						<span class="od-ag-start"><?php echo $odTime($odP . ".start", $odA["start"]); ?></span>
						<?php if ($odA["end"] !== "" || $odEdit) { ?><span class="od-ag-end"><?php echo $odTime($odP . ".end", $odA["end"]); ?></span><?php } ?>
					</div>
					<div class="od-ag-body">
						<span class="od-badge" data-od-badge hidden></span>
						<h3 class="od-ag-title"><?php echo $odF($odP . ".title", $odA["titleMn"], $odA["titleEn"], false, "Гарчиг"); ?></h3>
						<?php if ($odHas($odA["bodyMn"], $odA["bodyEn"])) { ?>
						<p class="od-ag-text"><?php echo $odF($odP . ".body", $odA["bodyMn"], $odA["bodyEn"], true, "Тайлбар"); ?></p>
						<?php } ?>
						<?php echo $odWhere($odA["spot"], "agenda", $odA); ?>
					</div>
				</li>
				<?php } ?>
			</ol>
			<?php echo $odAdd("agenda", "Хөтөлбөр нэмэх"); ?>
		</div>
	</section>
	<?php } ?>

	<!-- ================= Схем ================= -->
	<?php if ($odHasMap) { $odFirst = $odFloorList[0]; ?>
	<section class="od-view od-section od-section-map" id="map" aria-labelledby="od-h-map"<?php echo $odView("map"); ?>>
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-map"><?php echo $odF("s.mapTitle", $odSet["mapTitleMn"], $odSet["mapTitleEn"], false, "Хэсгийн гарчиг"); ?></h2>
			<?php if ($odHas($odSet["mapLeadMn"], $odSet["mapLeadEn"])) { ?>
			<p class="od-lead"><?php echo $odF("s.mapLead", $odSet["mapLeadMn"], $odSet["mapLeadEn"], false, "Тайлбар мөр"); ?></p>
			<?php } ?>
			<?php if ($odEdit) { ?>
			<div class="ode-maptools">
				<button type="button" class="ode-add ode-add-spot" data-ode-addspot><i class="fa fa-plus" aria-hidden="true"></i> Цэг нэмэх</button>
				<span class="ode-maphint" data-ode-maphint>Цэгийг чирж байрлуулна. Давхар солихдоо схемийн дээд талын давхрын нэр дээр дарна.</span>
			</div>
			<?php } ?>
		</div>

		<div class="od-map-frame">
			<div class="fp-hero od-fp" <?php echo $odEdit ? "data-ode-map" : "data-fp-root"; ?> aria-label="Өдөрлөгийн схем / Open day map">
				<div class="fp-stage<?php if ($odEdit) echo " fp-edit"; ?>" data-fp-stage>
					<div class="fp-floors">
						<?php foreach ($odFloorList as $odI => $odF2) { ?>
						<div class="fp-floor<?php echo $odI === 0 ? " is-active" : ""; ?><?php if ($odEdit) echo " is-ready"; ?>" data-fp-floor="<?php echo OpenDayCore::esc($odF2["key"]); ?>" data-phase="OVERVIEW"<?php echo $odI === 0 ? "" : ' aria-hidden="true"'; ?>>
							<div class="fp-world" data-fp-world>
								<img class="fp-image" <?php echo $odI === 0 || $odEdit ? "src" : "data-src"; ?>="<?php echo OpenDayCore::esc($odEdit && $odF2["imageMidUrl"] !== "" ? $odF2["imageMidUrl"] : $odF2["imageUrl"]); ?>"
									width="<?php echo (int)$odF2["imageWidth"]; ?>" height="<?php echo (int)$odF2["imageHeight"]; ?>"
									alt="<?php echo OpenDayCore::esc($odF2["floorTitle"]); ?>" decoding="async" draggable="false">
							</div>
							<div class="fp-spots" data-fp-spots></div>
							<p class="fp-fail" role="status">Схемийг ачаалж чадсангүй. / The map could not be loaded.</p>
						</div>
						<?php } ?>
					</div>

					<div class="fp-title">
						<button type="button" class="fp-title-btn" data-fp-floor-switch<?php echo count($odFloorList) < 2 ? " disabled" : ""; ?>>
							<span class="fp-title-text" data-fp-title><?php echo OpenDayCore::esc($odFirst["floorTitle"]); ?></span>
							<span class="fp-title-go" data-fp-title-go aria-hidden="true"<?php echo count($odFloorList) < 2 ? " hidden" : ""; ?>>
								<svg viewBox="0 0 24 24"><path d="M12 19V5M6 11l6-6 6 6"/></svg><span data-fp-title-next></span>
							</span>
						</button>
					</div>

					<?php if (!$odEdit) { ?>
					<div class="fp-info" data-fp-info aria-live="polite" aria-atomic="true">
						<div class="fp-info-inner">
							<h2 class="fp-info-en" data-fp-en></h2>
							<p class="fp-info-mn" data-fp-mn></p>
							<p class="fp-info-desc" data-fp-desc></p>
						</div>
					</div>

					<div class="fp-nav" role="group" aria-label="Цэг сонгох / Browse points">
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
					<?php } ?>
				</div>

				<script type="application/json" data-fp-data><?php echo OpenDayCore::jsonForHtml(array("lang" => "mn", "floors" => $odFloorList)); ?></script>
			</div>
		</div>

		<div class="od-wrap">
			<div class="od-places">
				<?php foreach ($odFloorList as $odF2) { if (count($odF2["hotspots"]) === 0 && !$odEdit) { continue; } ?>
				<div class="od-places-floor">
					<h3 class="od-places-h"><?php echo OpenDayCore::esc($odF2["number"]); ?><small>F</small></h3>
					<ul class="od-places-list">
						<?php foreach ($odF2["hotspots"] as $odH) { $odS = $odSpots[$odH["slug"]]; ?>
						<?php if ($odEdit) { $odP = "spot." . $odS["_i"]; ?>
						<li class="ode-spot<?php echo $odOff($odS); ?>"<?php echo $odRow("spot", $odS); ?> data-ode-slug="<?php echo OpenDayCore::esc($odS["slug"]); ?>">
							<div class="od-place">
								<button type="button" class="ode-icon" data-ode-pick="icon" data-ode-path="<?php echo $odP; ?>.icon" title="Icon солих"><i class="<?php echo OpenDayCore::esc(OpenDayCore::iconClass($odS["icon"])); ?>" aria-hidden="true"></i></button>
								<span class="od-place-name">
									<span class="ode-place-title"><?php echo $odF($odP . ".title", $odS["titleMn"], $odS["titleEn"], false, "Нэр"); ?></span>
									<span class="ode-place-desc"><?php echo $odF($odP . ".body", $odS["bodyMn"], $odS["bodyEn"], true, "Тайлбар (схем дээр дарахад гарна)"); ?></span>
								</span>
								<button type="button" class="ode-floor" data-ode-pick="floor" data-ode-path="<?php echo $odP; ?>.floor" title="Давхар солих"><?php echo OpenDayCore::esc($odF2["number"]); ?>F</button>
							</div>
						</li>
						<?php } else { ?>
						<li>
							<button type="button" class="od-place" data-od-spot="<?php echo OpenDayCore::esc($odS["slug"]); ?>">
								<i class="<?php echo OpenDayCore::esc(OpenDayCore::iconClass($odS["icon"])); ?>" aria-hidden="true"></i>
								<span class="od-place-name"><?php echo $odT($odS["titleMn"], $odS["titleEn"]); ?></span>
								<span class="od-place-go" aria-hidden="true"><i class="fa fa-angle-right"></i></span>
							</button>
						</li>
						<?php } ?>
						<?php } ?>
					</ul>
					<?php if ($odEdit && count($odF2["hotspots"]) === 0) { ?>
					<p class="ode-note">Энэ давхарт цэг алга — "Цэг нэмэх" дараад схем дээр дарна.</p>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>

	<!-- ================= Мэдээлэл ================= -->
	<?php if ($odEdit || count($odItems["info"]) > 0) { ?>
	<section class="od-view od-section" id="info" aria-labelledby="od-h-info"<?php echo $odView("info"); ?>>
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-info"><?php echo $odF("s.infoTitle", $odSet["infoTitleMn"], $odSet["infoTitleEn"], false, "Хэсгийн гарчиг"); ?></h2>
			<div class="od-cards">
				<?php foreach ($odItems["info"] as $odA) { $odP = "info." . $odA["_i"]; ?>
				<div class="od-card<?php echo $odOff($odA); ?>"<?php echo $odRow("info", $odA); ?>>
					<?php if ($odEdit) { ?>
					<button type="button" class="od-card-icon ode-icon <?php echo OpenDayCore::esc(OpenDayCore::iconClass($odA["icon"])); ?>" data-ode-pick="icon" data-ode-path="<?php echo $odP; ?>.icon" title="Icon солих"></button>
					<?php } else { ?>
					<i class="od-card-icon <?php echo OpenDayCore::esc(OpenDayCore::iconClass($odA["icon"])); ?>" aria-hidden="true"></i>
					<?php } ?>
					<h3 class="od-card-title"><?php echo $odF($odP . ".title", $odA["titleMn"], $odA["titleEn"], false, "Гарчиг"); ?></h3>
					<?php if ($odHas($odA["bodyMn"], $odA["bodyEn"])) { ?>
					<p class="od-card-text"><?php echo $odF($odP . ".body", $odA["bodyMn"], $odA["bodyEn"], true, "Агуулга"); ?></p>
					<?php } ?>
					<?php echo $odWhere($odA["spot"], "info", $odA); ?>
				</div>
				<?php } ?>
			</div>
			<?php echo $odAdd("info", "Карт нэмэх"); ?>
		</div>
	</section>
	<?php } ?>

	<!-- ================= Асуулт ================= -->
	<?php if ($odEdit || count($odItems["faq"]) > 0 || $odAskOn) { ?>
	<section class="od-view od-section" id="faq" aria-labelledby="od-h-faq"<?php echo $odView("faq"); ?>>
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-faq"><?php echo $odF("s.faqTitle", $odSet["faqTitleMn"], $odSet["faqTitleEn"], false, "Хэсгийн гарчиг"); ?></h2>
			<div class="od-faq">
				<?php foreach ($odItems["faq"] as $odA) { $odP = "faq." . $odA["_i"]; ?>
				<?php if ($odEdit) { ?>
				<div class="od-qa ode-qa<?php echo $odOff($odA); ?>"<?php echo $odRow("faq", $odA); ?>>
					<div class="ode-q"><span class="od-q"><?php echo $odF($odP . ".title", $odA["titleMn"], $odA["titleEn"], false, "Асуулт"); ?></span></div>
					<div class="od-a"><?php echo $odF($odP . ".body", $odA["bodyMn"], $odA["bodyEn"], true, "Хариулт"); ?></div>
				</div>
				<?php } else { ?>
				<details class="od-qa">
					<summary><span class="od-q"><?php echo $odT($odA["titleMn"], $odA["titleEn"]); ?></span><i class="fa fa-plus" aria-hidden="true"></i></summary>
					<div class="od-a"><?php echo OpenDayCore::bi($odA["bodyMn"], $odA["bodyEn"], true); ?></div>
				</details>
				<?php } ?>
				<?php } ?>
			</div>
			<?php echo $odAdd("faq", "Асуулт нэмэх"); ?>

			<?php if ($odAskOn || $odEdit) { ?>
			<div class="od-ask<?php if ($odEdit && !$odAskOn) echo " od-off"; ?>" id="ask">
				<?php if ($odEdit) { ?>
				<label class="ode-switch" title="Зочдоос асуулт хүлээн авах эсэх">
					<input type="checkbox" data-ode-toggle="s.askOn"<?php if ($odAskOn) echo " checked"; ?>>
					<span>Асуулт хүлээн авах — <?php echo $odAskOn ? "асаалттай" : "унтраалттай (зочдод харагдахгүй)"; ?></span>
				</label>
				<?php } ?>
				<h3 class="od-ask-title"><?php echo $odF("s.askTitle", $odSet["askTitleMn"], $odSet["askTitleEn"], false, "Гарчиг"); ?></h3>
				<?php if ($odHas($odSet["askLeadMn"], $odSet["askLeadEn"])) { ?>
				<p class="od-ask-lead"><?php echo $odF("s.askLead", $odSet["askLeadMn"], $odSet["askLeadEn"], false, "Тайлбар мөр"); ?></p>
				<?php } ?>
				<form class="od-ask-form" data-od-ask method="post" action="/openday" novalidate>
					<fieldset<?php if ($odEdit) echo " disabled"; ?>>
						<input type="hidden" name="odAction" value="ask">
						<input type="hidden" name="odTs" value="<?php echo time(); ?>">
						<input type="hidden" name="lang" value="mn" data-od-ask-lang>
						<div class="od-hp" aria-hidden="true"><input type="text" name="odWebsite" tabindex="-1" autocomplete="off"></div>
						<label class="od-sr" for="od-ask-q"><?php echo $odT("Таны асуулт", "Your question"); ?></label>
						<textarea class="od-input" id="od-ask-q" name="question" rows="4" maxlength="1000" required
							data-ph-mn="Асуултаа энд бичнэ үү…" data-ph-en="Type your question…" placeholder="Асуултаа энд бичнэ үү…"></textarea>
						<label class="od-sr" for="od-ask-n"><?php echo $odT("Нэр (заавал биш)", "Name (optional)"); ?></label>
						<input class="od-input" id="od-ask-n" type="text" name="name" maxlength="80" autocomplete="name"
							data-ph-mn="Нэр (заавал биш)" data-ph-en="Name (optional)" placeholder="Нэр (заавал биш)">
						<button type="submit" class="od-btn"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php echo $odT("Илгээх", "Send"); ?></button>
						<p class="od-ask-msg" data-od-ask-msg role="status" hidden></p>
					</fieldset>
				</form>
			</div>
			<?php } ?>
		</div>
	</section>
	<?php } ?>

</main>

<footer class="od-footer">
	<div class="od-wrap"><?php echo $odF("s.footer", $odSet["footerMn"], $odSet["footerEn"], false, "Хөл хэсгийн текст"); ?></div>
</footer>

<?php if ($odEdit) { ?>
<script type="application/json" data-ode-meta><?php
	$odMetaSpots = array();
	foreach ($odItems["spot"] as $odS) {
		$odMetaSpots[] = array("i" => $odS["_i"], "slug" => $odS["slug"], "floor" => $odS["floor"], "x" => $odS["x"], "y" => $odS["y"],
			"titleMn" => $odS["titleMn"], "titleEn" => $odS["titleEn"], "enabled" => (bool)$odS["enabled"]);
	}
	$odMetaIcons = array();
	foreach (OpenDayCore::icons() as $odKey => $odIc) {
		$odMetaIcons[] = array("key" => $odKey, "fa" => $odIc[0], "label" => $odIc[1]);
	}
	$odMetaFloors = array();
	foreach ($odFloorList as $odF2) {
		$odMetaFloors[] = array("key" => $odF2["key"], "number" => $odF2["number"]);
	}
	echo OpenDayCore::jsonForHtml(array(
		"spots"  => $odMetaSpots,
		"icons"  => $odMetaIcons,
		"floors" => $odMetaFloors,
		"date"   => $odSet["eventDate"],
		"start"  => $odSet["startTime"],
		"end"    => $odSet["endTime"]
	));
?></script>
<?php if ($odHasMap) { ?>
<script src="<?php echo OpenDayCore::esc($odAssetUrl("assets/js/floorplan/core.js")); ?>"></script>
<script src="<?php echo OpenDayCore::esc($odAssetUrl("assets/js/floorplan/viewer.js")); ?>"></script>
<?php } ?>
<script src="<?php echo OpenDayCore::esc($odAssetUrl("canvas.js")); ?>"></script>
<?php } else { ?>
<script type="application/json" data-od-data><?php echo OpenDayCore::jsonForHtml($odJs); ?></script>
<?php if ($odHasMap) { ?>
<script src="<?php echo OpenDayCore::esc($odAssetUrl("assets/js/floorplan/core.js")); ?>" defer></script>
<script src="<?php echo OpenDayCore::esc($odAssetUrl("assets/js/floorplan/viewer.js")); ?>" defer></script>
<script src="<?php echo OpenDayCore::esc($odAssetUrl("assets/js/floorplan/public.js")); ?>" defer></script>
<?php } ?>
<script src="<?php echo OpenDayCore::esc($odAssetUrl("assets/js/openday.js")); ?>" defer></script>
<?php } ?>
</body>
</html>
