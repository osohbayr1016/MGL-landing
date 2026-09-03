<?php
/**
 * /registration хуудасны ЛОКАЛ урьдчилан харах хувилбар.
 *
 * Өгөгдлийн сан ШААРДАХГҮЙ — блокуудыг гараар бичсэн. Зорилго нь
 * deploy хийхээс өмнө гүйлт, тогтмол дэвсгэр, хөтөлбөрийн хэсэг
 * зөв ажиллаж байгааг нүдээр шалгах.
 *
 *   php -S localhost:8000 router.php
 *   http://localhost:8000/registration          — зочны харагдац
 *   http://localhost:8000/registration?edit=1   — засварлах горим (багажтай)
 *
 * Жинхэнэ хуудсыг skin/new/registration.php зурдаг.
 */

$pvEdit = isset($_GET["edit"]);
$pvBg   = "https://cp.mglenc.com/postpic/image/content/MITIC%20profile.jpg";

/* ?bg=grid — дэвсгэр үнэхээр байрандаа үлдэж байгааг шалгах торон зураг */
if (isset($_GET["bg"]) && $_GET["bg"] === "grid") {
	$pvBg = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNDQwIiBoZWlnaHQ9IjkwMCIgdmlld0JveD0iMCAwIDE0NDAgOTAwIj4KICA8ZGVmcz4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZyIgeDE9IjAiIHkxPSIwIiB4Mj0iMSIgeTI9IjEiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiMxRTNBNUYiLz4KICAgICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjN0EzQjFFIi8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogICAgPHBhdHRlcm4gaWQ9InAiIHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KICAgICAgPHJlY3Qgd2lkdGg9IjEyMCIgaGVpZ2h0PSIxMjAiIGZpbGw9Im5vbmUiLz4KICAgICAgPHBhdGggZD0iTTEyMCAwSDBWMTIwIiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS1vcGFjaXR5PSIuMjgiIHN0cm9rZS13aWR0aD0iMiIvPgogICAgPC9wYXR0ZXJuPgogIDwvZGVmcz4KICA8cmVjdCB3aWR0aD0iMTQ0MCIgaGVpZ2h0PSI5MDAiIGZpbGw9InVybCgjZykiLz4KICA8cmVjdCB3aWR0aD0iMTQ0MCIgaGVpZ2h0PSI5MDAiIGZpbGw9InVybCgjcCkiLz4KICA8Y2lyY2xlIGN4PSI3MjAiIGN5PSI0NTAiIHI9IjIzMCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRkZEMTY2IiBzdHJva2Utd2lkdGg9IjEwIi8+CiAgPHRleHQgeD0iNzIwIiB5PSI0NzAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMjAiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSIjRkZEMTY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5CRzwvdGV4dD4KICA8dGV4dCB4PSI3MjAiIHk9IjEyMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjU0IiBmaWxsPSIjZmZmZmZmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5UT1AgRURHRTwvdGV4dD4KICA8dGV4dCB4PSI3MjAiIHk9Ijg0MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjU0IiBmaWxsPSIjZmZmZmZmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5CT1RUT00gRURHRTwvdGV4dD4KPC9zdmc+Cg==";
}

/* ?anim=0 — гарч ирэх анимацийг алгасаж, бүх текстийг шууд харуулна */
$pvAnim = !isset($_GET["anim"]) || $_GET["anim"] !== "0";

/* Засварлах горимд байгаа мэт data-* атрибут гаргана */
function pvEd($key, $mode = "text")
{
	global $pvEdit;

	return $pvEdit ? ' data-reg-edit="block:1:' . $key . ':' . $mode . '"' : "";
}

$pvAgenda = array(
	array("09:00", "2026.09.25", "Үндсэн танхим", "<p><strong>Бүртгэл</strong> — зочдыг угтах, кофе</p>"),
	array("10:00", "2026.09.25", "Үндсэн танхим", "<p><strong>Нээлтийн ёслол</strong> — удирдлагын үг</p>"),
	array("12:00", "2026.09.25", "Ресторан",      "<p><strong>Өдрийн хоол</strong></p>"),
	array("14:00", "2026.09.25", "3 давхар",      "<p><strong>Оффис үзэх аялал</strong></p>")
);
?><!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title>Preview — /registration</title>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="/assets/client/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="/assets/css/registration.css" rel="stylesheet">
<link href="/assets/css/registration-scroll.css" rel="stylesheet">
<link href="/assets/css/registration-agenda.css" rel="stylesheet">
<?php if ($pvEdit) { ?>
<link href="/assets/css/registration-edit.css" rel="stylesheet">
<link href="/assets/css/registration-rte.css" rel="stylesheet">
<?php } ?>

<style>
:root{
	--reg-bg: #0E0E0E;
	--reg-surface: #171717;
	--reg-text: #FFFFFF;
	--reg-muted: #A8A8A8;
	--reg-border: #2A2A2A;
	--reg-accent: #FE5925;
	--reg-accent-text: #FFFFFF;
	--reg-input-bg: #FFFFFF;
	--reg-input-text: #111111;
	--reg-max: 1080px;
	--reg-radius: 4px;
	--reg-title-weight: 800;
	--reg-body-weight: 500;
	--reg-title-size: 56px;
	--reg-ls: 0em;
}
</style>
</head>
<body class="reg-body reg-upper reg-has-bg<?php if ($pvEdit) echo " reg-editing"; ?>">

<div class="reg-page-bg" id="regPageBg" data-reg-bg="page"<?php if ($pvEdit) echo ' data-reg-media="setting:0:pageBgPic:both"'; ?>>
	<div class="reg-page-bg-media" style="background-image:url('<?php echo $pvBg; ?>');background-position:center;"></div>
	<span class="reg-page-bg-shade" style="opacity:.55"></span>
</div>

<div class="reg-page" data-reg-snap="<?php echo $pvEdit ? "0" : "1"; ?>" data-reg-dots="<?php echo $pvEdit ? "0" : "1"; ?>">

	<!-- ---------------- Hero ---------------- -->
	<section class="reg-hero reg-scroll-section reg-hero-a-center reg-hero-v-center"
		style="color:#FFFFFF"<?php if ($pvEdit) echo ' data-reg-media="setting:0:pageBgPic:both"'; ?> data-reg-bg="page">

		<div class="reg-scroll-content reg-hero-inner">
			<div class="reg-wrap">
				<p class="reg-hero-eyebrow"<?php echo pvEd("eyebrow"); ?>>MGL E&amp;C</p>
				<h1 class="reg-hero-title"<?php echo pvEd("title"); ?>>Шинэ оффисын нээлтийн өдөрлөг</h1>
				<p class="reg-hero-sub"<?php echo pvEd("subtitle"); ?>>Бидний шинэ гэрт хамтдаа тэмдэглэе.</p>
				<ul class="reg-hero-meta">
					<li><i class="fa fa-calendar"></i> <span<?php echo pvEd("dateText"); ?>>2026.09.25</span></li>
					<li><i class="fa fa-map-marker"></i> <span<?php echo pvEd("locationText"); ?>>Улаанбаатар</span></li>
				</ul>
				<a class="reg-btn reg-hero-btn" href="#registration-form"<?php echo pvEd("btnText"); ?>>Бүртгүүлэх</a>
			</div>
		</div>

		<?php if (!$pvEdit) { ?>
		<span class="reg-scroll-cue" aria-hidden="true"><i class="fa fa-angle-down"></i></span>
		<?php } ?>
	</section>

	<!-- ---------------- Мэдээлэл ---------------- -->
	<section class="reg-scroll-section reg-section reg-info">
		<div class="reg-section-bg"></div>
		<div class="reg-scroll-content" style="padding-top:80px;padding-bottom:80px;">
			<div class="reg-wrap">
				<h2 class="reg-section-title"<?php echo pvEd("title"); ?>>Арга хэмжээний мэдээлэл</h2>
				<div class="reg-info-grid" style="grid-template-columns:repeat(3, minmax(0,1fr))">
					<?php foreach (array(
						array("fa fa-calendar", "Огноо", "2026 оны 9 сарын 25"),
						array("fa fa-clock-o", "Цаг", "09:00 — 17:00"),
						array("fa fa-map-marker", "Байршил", "Сүхбаатар дүүрэг, MGL Tower")
					) as $it) { ?>
					<div class="reg-info-item">
						<i class="reg-info-icon <?php echo $it[0]; ?>"></i>
						<h3 class="reg-info-label"<?php echo pvEd("label"); ?>><?php echo $it[1]; ?></h3>
						<p class="reg-info-value"<?php echo pvEd("value"); ?>><?php echo $it[2]; ?></p>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>

	<!-- ---------------- Хөтөлбөр ---------------- -->
	<section class="reg-scroll-section reg-section reg-agenda">
		<div class="reg-section-bg"></div>
		<div class="reg-scroll-content" style="padding-top:80px;padding-bottom:80px;">
			<div class="reg-wrap">
				<h2 class="reg-section-title"<?php echo pvEd("title"); ?>>Арга хэмжээний хөтөлбөр</h2>

				<ol class="reg-timeline">
					<?php foreach ($pvAgenda as $i => $row) { ?>
					<li class="reg-tl-item">
						<span class="reg-tl-mark" aria-hidden="true"></span>
						<div class="reg-tl-when">
							<span class="reg-tl-time"<?php echo pvEd("time"); ?>><?php echo $row[0]; ?></span>
							<span class="reg-tl-date"<?php echo pvEd("date"); ?>><?php echo $row[1]; ?></span>
						</div>
						<div class="reg-tl-body">
							<div class="reg-tl-text reg-rte"<?php echo pvEd("body", "html"); ?>><?php echo $row[3]; ?></div>
							<span class="reg-tl-loc"><i class="fa fa-map-marker"></i> <span<?php echo pvEd("location"); ?>><?php echo $row[2]; ?></span></span>
						</div>
					</li>
					<?php } ?>
				</ol>

				<div class="reg-program reg-program-filled"
					style="max-width:760px;text-align:left;padding:32px;margin-left:auto;margin-right:auto;background-color:rgba(0,0,0,0.7);"
					<?php if ($pvEdit) { ?>data-reg-panel="1" data-align="left" data-pos="center"
					data-width="760" data-bg="#000000" data-opacity="70" data-pad="32"<?php } ?>>
					<h3 class="reg-program-title"<?php echo pvEd("programTitle"); ?>>Хөтөлбөр</h3>
					<div class="reg-program-body reg-rte"<?php echo pvEd("program", "html"); ?>>
						<p>Өдөрлөгийн дэлгэрэнгүй хөтөлбөрөө энд бичнэ. Текст сонгоод дээр нь гарч ирэх
						багажаар <strong>үсгийн хэмжээ</strong>, <em>загвар</em>, өнгө, зэрэгцүүлэлтээ өөрчилнө.</p>
						<ul>
							<li>Жагсаалт нэмэх</li>
							<li>Гарчиг болгох</li>
							<li>Холбоос тавих</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ---------------- Форм ---------------- -->
	<section id="registration-form" class="reg-section reg-form-section reg-scroll-section reg-scroll-section--fluid">
		<div class="reg-section-bg"></div>
		<div class="reg-scroll-content" style="padding-top:80px;padding-bottom:100px;">
			<div class="reg-wrap">
				<div class="reg-form-box" style="max-width:620px">
					<h2 class="reg-section-title"<?php echo pvEd("title"); ?>>Бүртгүүлэх</h2>
					<p<?php echo pvEd("subtitle"); ?>>Доорх мэдээллийг бөглөн бүртгүүлнэ үү.</p>
					<form class="reg-form" onsubmit="return false">
						<div class="reg-field"><label class="reg-label">Овог нэр</label><input class="reg-input" type="text"></div>
						<div class="reg-field"><label class="reg-label">Утас</label><input class="reg-input" type="text"></div>
						<div class="reg-field"><label class="reg-label">И-мэйл</label><input class="reg-input" type="text"></div>
						<button class="reg-btn" type="submit">Бүртгүүлэх</button>
					</form>
				</div>
			</div>
		</div>
	</section>

</div>

<footer class="reg-footer">
	<div class="reg-wrap"><span<?php echo pvEd("footerText"); ?>>© MGL E&amp;C LLC</span></div>
</footer>

<?php if ($pvEdit) { ?>
<div id="regEditBar" class="reg-editbar" data-nonce="preview" data-maxbytes="8388608" data-maxtext="8 MB">
	<span class="reg-editbar-logo">MGL</span>
	<span class="reg-editbar-msg" id="regEditMsg">УРЬДЧИЛАН ХАРАХ — хадгалах ажиллахгүй. Текст дээр дарж багажаа шалгана уу.</span>
	<span class="reg-editbar-actions">
		<button type="button" class="reg-eb-main" id="regEditSave" disabled>Хадгалах</button>
		<button type="button" class="reg-eb-ghost" id="regEditUndo" disabled>Болих</button>
		<button type="button" class="reg-eb-ghost" id="regEditBg" title="Хуудасны дэвсгэр солих"><i class="fa fa-picture-o"></i> Дэвсгэр</button>
		<a class="reg-eb-ghost" href="/registration">Гарах</a>
	</span>
</div>
<input type="file" id="regEditFile" class="reg-hidden-file" accept="image/*" aria-hidden="true" tabindex="-1">
<?php } ?>

<?php if (isset($_GET["shift"])) { /* preview: агуулгыг дээш өргөж "гүйлт"-ийг дуурайна */ ?>
<style>.reg-page{margin-top:-<?php echo (int)$_GET["shift"]; ?>px}</style>
<?php } ?>
<?php if (!$pvAnim) { ?>
<style>body.reg-scroll-ready .reg-rv{opacity:1 !important;transform:none !important;}</style>
<?php } ?>
<script src="/assets/js/registration-scroll.js"></script>
<?php if (isset($_GET["y"])) { /* зөвхөн preview: тодорхой байрлал руу гүйлгэнэ */ ?>
<script>window.addEventListener("load", function () { window.scrollTo({ top: <?php echo (int)$_GET["y"]; ?>, behavior: "instant" }); });</script>
<?php } ?>
<?php if ($pvEdit) { ?>
<script src="/assets/js/registration-edit.js"></script>
<script src="/assets/js/registration-rte.js"></script>
<?php if (isset($_GET["demo"])) { /* preview: багажийг автоматаар нээж үзүүлнэ */ ?>
<script>
window.addEventListener("load", function () {
	var body = document.querySelector(".reg-program-body");
	var p = body.querySelector("p");
	body.focus();
	var r = document.createRange();
	r.setStart(p.firstChild, 0);
	r.setEnd(p.firstChild, 40);
	var sel = window.getSelection();
	sel.removeAllRanges();
	sel.addRange(r);
	body.dispatchEvent(new Event("mouseup"));
	if (location.search.indexOf("panel=1") >= 0) {
		document.querySelector(".reg-lay-open").click();
	}
});
</script>
<?php } ?>
<?php } ?>
</body>
</html>
