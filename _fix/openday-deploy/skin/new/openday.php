<?php
/**
 * Open Office Day — зочдын тур хуудасны layout (/openday).
 *
 * Сайтын header/footer/навигацийг ЗОРИУДААР оруулаагүй — зөвхөн шууд линк,
 * QR-аар нээгдэнэ. Харагдац нь бүртгэлийн хуудастай (/registration) ижил:
 * тэр хуудасны дэвсгэр зураг, өнгийг уншиж ашиглана (pages/openday/sys.php).
 *
 * Хоёр хэл: текст бүр монгол + англи хоёуланг нь гаргаж (OpenDayCore::bi),
 * <html data-lang> -аар нэгийг нь CSS нууна. Хэл солих товч хуудсыг дахин
 * ачаалахгүй (assets/js/openday.js).
 */

$odBase = rtrim(dirname(dirname(__DIR__)), "/\\") . "/";
$odVer = function ($rel) use ($odBase) {
	$f = $odBase . ltrim($rel, "/");
	return is_file($f) ? (int)filemtime($f) : 1;
};

$odT = function ($mn, $en) {
	return OpenDayCore::bi($mn, $en);
};

/* "2026-10-17" -> "2026.10.17" */
$odDateText = $odSet["eventDate"] !== "" ? str_replace("-", ".", $odSet["eventDate"]) : "";
$odTimeText = OpenDayCore::timeRange($odSet["startTime"], $odSet["endTime"]);

/** Байршлын товч: тухайн цэгийг схем дээр нээнэ. */
$odWhere = function ($slug) use ($odSpots, $odFloors) {
	if ($slug === "" || !isset($odSpots[$slug])) {
		return "";
	}
	$s = $odSpots[$slug];
	$num = isset($odFloors[$s["floor"]]) ? $odFloors[$s["floor"]]["number"] . "F" : "";

	return '<button type="button" class="od-where" data-od-spot="' . OpenDayCore::esc($slug) . '">'
		. '<i class="fa fa-map-marker" aria-hidden="true"></i>'
		. '<span class="od-where-name">' . OpenDayCore::bi($s["titleMn"], $s["titleEn"]) . '</span>'
		. ($num !== "" ? '<span class="od-where-floor">' . OpenDayCore::esc($num) . '</span>' : "")
		. '</button>';
};

$odHasMap = count($odFloorList) > 0 && count($odSpots) > 0;

$odNav = array();
if (count($odItems["agenda"]) > 0)   { $odNav[] = array("schedule", "Хөтөлбөр", "Programme"); }
if ($odHasMap)                       { $odNav[] = array("map", "Схем", "Map"); }
if (count($odItems["activity"]) > 0) { $odNav[] = array("todo", "Хийх зүйлс", "To do"); }
if (count($odItems["info"]) > 0)     { $odNav[] = array("info", "Мэдээлэл", "Info"); }
if (count($odItems["faq"]) > 0)      { $odNav[] = array("faq", "Асуулт", "FAQ"); }

$odTitlePlain = $odSet["titleMn"] !== "" ? $odSet["titleMn"] : $odSet["titleEn"];

$odBgStyle = "";
if ($odLook["bgPicUrl"] !== "") {
	$odBgStyle .= "background-image:url('" . OpenDayCore::esc($odLook["bgPicUrl"]) . "');";
}
$odBgStyle .= "background-position:" . OpenDayCore::esc($odLook["pos"]) . ";";

/* openday.js-д: цаг, цэгийн хоёр хэлтэй нэр/тайлбар */
$odJs = array(
	"date"     => $odSet["eventDate"],
	"start"    => $odSet["startTime"],
	"end"      => $odSet["endTime"],
	"agenda"   => $odAgendaJs,
	"activity" => $odActivityJs,
	"spots"    => $odSpotJs
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

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&amp;family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&amp;display=swap" rel="stylesheet">
<link href="/assets/client/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
<?php if ($odHasMap) { ?>
<link href="/assets/css/floorplan.css?v=<?php echo $odVer("assets/css/floorplan.css"); ?>" rel="stylesheet">
<?php } ?>
<link href="/assets/css/openday.css?v=<?php echo $odVer("assets/css/openday.css"); ?>" rel="stylesheet">
<style>:root{--od-accent:<?php echo OpenDayCore::esc($odLook["accent"]); ?>;}</style>
</head>
<body class="od-body<?php if ($odLook["bgPicUrl"] !== "" || $odLook["bgVideoUrl"] !== "") echo " od-has-bg"; ?>">

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
		<a class="od-brand" href="#top" aria-label="<?php echo OpenDayCore::esc($odTitlePlain); ?>">
			<?php if ($odLook["logoUrl"] !== "") { ?>
			<img src="<?php echo OpenDayCore::esc($odLook["logoUrl"]); ?>" alt="MGL E&amp;C" height="28">
			<?php } else { ?>
			<span class="od-brand-text">MGL E&amp;C</span>
			<?php } ?>
		</a>
		<?php if (count($odNav) > 0) { ?>
		<nav class="od-tabs" aria-label="<?php echo OpenDayCore::esc("Хуудасны хэсгүүд / Sections"); ?>">
			<?php foreach ($odNav as $odN) { ?>
			<a href="#<?php echo $odN[0]; ?>" data-od-tab="<?php echo $odN[0]; ?>"><?php echo $odT($odN[1], $odN[2]); ?></a>
			<?php } ?>
		</nav>
		<?php } ?>
		<button type="button" class="od-lang" data-od-lang aria-label="Switch language / Хэл солих">
			<span class="od-mn">EN</span><span class="od-en">MN</span>
		</button>
	</div>
</header>

<main class="od-page" id="top">

	<section class="od-hero">
		<div class="od-wrap">
			<?php if ($odSet["eyebrowMn"] !== "" || $odSet["eyebrowEn"] !== "") { ?>
			<p class="od-eyebrow"><?php echo $odT($odSet["eyebrowMn"], $odSet["eyebrowEn"]); ?></p>
			<?php } ?>
			<h1 class="od-title"><?php echo $odT($odSet["titleMn"], $odSet["titleEn"]); ?></h1>

			<ul class="od-meta">
				<?php if ($odDateText !== "") { ?>
				<li><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo OpenDayCore::esc($odDateText); ?></li>
				<?php } ?>
				<?php if ($odTimeText !== "") { ?>
				<li><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo OpenDayCore::esc($odTimeText); ?></li>
				<?php } ?>
				<?php if ($odSet["locationMn"] !== "" || $odSet["locationEn"] !== "") { ?>
				<li><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $odT($odSet["locationMn"], $odSet["locationEn"]); ?></li>
				<?php } ?>
			</ul>

			<?php if ($odSet["introMn"] !== "" || $odSet["introEn"] !== "") { ?>
			<p class="od-intro"><?php echo OpenDayCore::bi($odSet["introMn"], $odSet["introEn"], true); ?></p>
			<?php } ?>

			<?php if (count($odItems["agenda"]) > 0) { ?>
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
		</div>
	</section>

	<?php if (count($odItems["agenda"]) > 0) { ?>
	<section class="od-section" id="schedule" aria-labelledby="od-h-schedule">
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-schedule"><?php echo $odT("Хөтөлбөр", "Programme"); ?></h2>
			<ol class="od-agenda" data-od-agenda>
				<?php foreach ($odItems["agenda"] as $odI => $odA) { ?>
				<li class="od-ag" data-od-i="<?php echo $odI; ?>">
					<div class="od-ag-time">
						<span class="od-ag-start"><?php echo OpenDayCore::esc($odA["start"]); ?></span>
						<?php if ($odA["end"] !== "") { ?><span class="od-ag-end"><?php echo OpenDayCore::esc($odA["end"]); ?></span><?php } ?>
					</div>
					<div class="od-ag-body">
						<span class="od-badge" data-od-badge hidden></span>
						<h3 class="od-ag-title"><?php echo $odT($odA["titleMn"], $odA["titleEn"]); ?></h3>
						<?php if ($odA["bodyMn"] !== "" || $odA["bodyEn"] !== "") { ?>
						<p class="od-ag-text"><?php echo OpenDayCore::bi($odA["bodyMn"], $odA["bodyEn"], true); ?></p>
						<?php } ?>
						<?php echo $odWhere($odA["spot"]); ?>
					</div>
				</li>
				<?php } ?>
			</ol>
		</div>
	</section>
	<?php } ?>

	<?php if ($odHasMap) { $odFirst = $odFloorList[0]; ?>
	<section class="od-section od-section-map" id="map" aria-labelledby="od-h-map">
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-map"><?php echo $odT("Хаана юу байна", "Where is what"); ?></h2>
			<p class="od-lead"><?php echo $odT(
				"Цэг дээр дарж дэлгэрэнгүйг харна. Давхар солихдоо дээд талын давхрын нэр эсвэл шатны тэмдэг дээр дарна.",
				"Tap a point to see what is there. To change floors, tap the floor name at the top or the staircase marker."
			); ?></p>
		</div>

		<div class="od-map-frame">
			<div class="fp-hero od-fp" data-fp-root aria-label="Өдөрлөгийн схем / Open day map">
				<div class="fp-stage" data-fp-stage>
					<div class="fp-floors">
						<?php foreach ($odFloorList as $odI => $odF) { ?>
						<div class="fp-floor<?php echo $odI === 0 ? " is-active" : ""; ?>" data-fp-floor="<?php echo OpenDayCore::esc($odF["key"]); ?>" data-phase="OVERVIEW"<?php echo $odI === 0 ? "" : ' aria-hidden="true"'; ?>>
							<div class="fp-world" data-fp-world>
								<img class="fp-image" <?php echo $odI === 0 ? "src" : "data-src"; ?>="<?php echo OpenDayCore::esc($odF["imageUrl"]); ?>"
									width="<?php echo (int)$odF["imageWidth"]; ?>" height="<?php echo (int)$odF["imageHeight"]; ?>"
									alt="<?php echo OpenDayCore::esc($odF["floorTitle"]); ?>" decoding="async" loading="lazy" draggable="false">
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
				</div>

				<script type="application/json" data-fp-data><?php echo OpenDayCore::jsonForHtml(array("lang" => "mn", "floors" => $odFloorList)); ?></script>
			</div>
		</div>

		<div class="od-wrap">
			<div class="od-places">
				<?php foreach ($odFloorList as $odF) { if (count($odF["hotspots"]) === 0) { continue; } ?>
				<div class="od-places-floor">
					<h3 class="od-places-h"><?php echo OpenDayCore::esc($odF["number"]); ?><small>F</small></h3>
					<ul class="od-places-list">
						<?php foreach ($odF["hotspots"] as $odH) { $odS = $odSpots[$odH["slug"]]; ?>
						<li>
							<button type="button" class="od-place" data-od-spot="<?php echo OpenDayCore::esc($odS["slug"]); ?>">
								<i class="<?php echo OpenDayCore::esc(OpenDayCore::iconClass($odS["icon"])); ?>" aria-hidden="true"></i>
								<span class="od-place-name"><?php echo $odT($odS["titleMn"], $odS["titleEn"]); ?></span>
								<span class="od-place-go" aria-hidden="true"><i class="fa fa-angle-right"></i></span>
							</button>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>

	<?php if (count($odItems["activity"]) > 0) { ?>
	<section class="od-section" id="todo" aria-labelledby="od-h-todo">
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-todo"><?php echo $odT("Хийж болох зүйлс", "Things to do"); ?></h2>
			<p class="od-lead"><?php echo $odT(
				"Санал болгох тур маршрут. Дарааллыг заавал дагах шаардлагагүй.",
				"A suggested route through the office. Feel free to go in any order."
			); ?></p>
			<ol class="od-route">
				<?php foreach ($odItems["activity"] as $odI => $odA) { ?>
				<li class="od-step" data-od-act="<?php echo $odI; ?>">
					<span class="od-step-n" aria-hidden="true"><?php echo $odI + 1; ?></span>
					<div class="od-step-body">
						<span class="od-badge" data-od-badge hidden></span>
						<h3 class="od-step-title"><?php echo $odT($odA["titleMn"], $odA["titleEn"]); ?></h3>
						<?php if ($odA["bodyMn"] !== "" || $odA["bodyEn"] !== "") { ?>
						<p class="od-step-text"><?php echo OpenDayCore::bi($odA["bodyMn"], $odA["bodyEn"], true); ?></p>
						<?php } ?>
						<div class="od-step-meta">
							<span class="od-when"><i class="fa fa-clock-o" aria-hidden="true"></i>
								<?php echo ($odA["start"] !== "" || $odA["end"] !== "")
									? OpenDayCore::esc(OpenDayCore::timeRange($odA["start"], $odA["end"]))
									: $odT("Өдөржин", "Any time"); ?>
							</span>
							<?php echo $odWhere($odA["spot"]); ?>
						</div>
					</div>
				</li>
				<?php } ?>
			</ol>
		</div>
	</section>
	<?php } ?>

	<?php if (count($odItems["info"]) > 0) { ?>
	<section class="od-section" id="info" aria-labelledby="od-h-info">
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-info"><?php echo $odT("Практик мэдээлэл", "Good to know"); ?></h2>
			<div class="od-cards">
				<?php foreach ($odItems["info"] as $odA) { ?>
				<div class="od-card">
					<i class="od-card-icon <?php echo OpenDayCore::esc(OpenDayCore::iconClass($odA["icon"])); ?>" aria-hidden="true"></i>
					<h3 class="od-card-title"><?php echo $odT($odA["titleMn"], $odA["titleEn"]); ?></h3>
					<?php if ($odA["bodyMn"] !== "" || $odA["bodyEn"] !== "") { ?>
					<p class="od-card-text"><?php echo OpenDayCore::bi($odA["bodyMn"], $odA["bodyEn"], true); ?></p>
					<?php } ?>
					<?php echo $odWhere($odA["spot"]); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>

	<?php if (count($odItems["faq"]) > 0) { ?>
	<section class="od-section" id="faq" aria-labelledby="od-h-faq">
		<div class="od-wrap">
			<h2 class="od-h2" id="od-h-faq"><?php echo $odT("Түгээмэл асуулт", "FAQ"); ?></h2>
			<div class="od-faq">
				<?php foreach ($odItems["faq"] as $odA) { ?>
				<details class="od-qa">
					<summary><span class="od-q"><?php echo $odT($odA["titleMn"], $odA["titleEn"]); ?></span><i class="fa fa-plus" aria-hidden="true"></i></summary>
					<div class="od-a"><?php echo OpenDayCore::bi($odA["bodyMn"], $odA["bodyEn"], true); ?></div>
				</details>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>

</main>

<footer class="od-footer">
	<div class="od-wrap"><?php echo $odT($odSet["footerMn"], $odSet["footerEn"]); ?></div>
</footer>

<script type="application/json" data-od-data><?php echo OpenDayCore::jsonForHtml($odJs); ?></script>
<?php if ($odHasMap) { ?>
<script src="/assets/js/floorplan/core.js?v=<?php echo $odVer("assets/js/floorplan/core.js"); ?>" defer></script>
<script src="/assets/js/floorplan/viewer.js?v=<?php echo $odVer("assets/js/floorplan/viewer.js"); ?>" defer></script>
<script src="/assets/js/floorplan/public.js?v=<?php echo $odVer("assets/js/floorplan/public.js"); ?>" defer></script>
<?php } ?>
<script src="/assets/js/openday.js?v=<?php echo $odVer("assets/js/openday.js"); ?>" defer></script>
</body>
</html>
