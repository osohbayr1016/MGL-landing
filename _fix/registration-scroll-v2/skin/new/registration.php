<?php
/**
 * Бүртгэлийн хуудасны бие даасан layout.
 *
 * Сайтын header/footer/навигацийг ЗОРИУДААР оруулаагүй — энэ хуудас
 * зөвхөн шууд линк, QR кодоор нээгддэг.
 *
 * Өнгө, хэмжээ бүгд CSS хувьсагчаар гардаг тул дизайнер CP Admin-аас
 * бүх зүйлийг өөрчилж чадна. Хамгийн сүүлд нь "Нэмэлт CSS" ордог тул
 * дизайнер өөрийн бичсэн CSS-ээр аль ч дүрмийг дарж бичих боломжтой.
 *
 * $regEdit үнэн бол хуудсан дээрээ шууд засварлах горим нэмэгдэнэ.
 */

$regUpper  = ((string)$regSet["themeUppercase"] === "1");
$regMaxW   = (int)$regSet["themeMaxWidth"] > 0 ? (int)$regSet["themeMaxWidth"] : 1080;
$regRadius = (int)$regSet["themeRadius"];
$regTitleSize = (int)$regSet["themeTitleSize"] > 0 ? (int)$regSet["themeTitleSize"] : 56;

/* Хуудасны тогтмол дэвсгэр — sys.php бэлддэг. Шууд дуудсан тохиолдолд хоосон. */
if (!isset($regPageBg) || !is_array($regPageBg)) {
	$regPageBg = array("pic" => "", "video" => "", "overlay" => 55, "blur" => 0, "pos" => "center");
}

$regBgPic   = $regPageBg["pic"];
$regBgVideo = $regPageBg["video"];
$regHasBg   = ($regBgPic != "" || $regBgVideo != "");

/* Гүйлтийн горим — засварлаж байхад snap-ыг унтраана */
$regSnap = ((string)RegistrationCore::val($regSet, "scrollSnap", "1") === "1") && !$regEdit;
$regDots = ((string)RegistrationCore::val($regSet, "scrollDots", "1") === "1") && !$regEdit;
?><!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />

<!-- Хайлтын систем болон нийгмийн сүлжээнд гаргахгүй -->
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet" />
<meta name="googlebot" content="noindex, nofollow" />

<title><?php echo RegistrationCore::esc($regSet["metaTitle"]); ?></title>
<?php if (trim($regSet["metaDesc"]) != "") { ?>
<meta name="description" content="<?php echo RegistrationCore::esc($regSet["metaDesc"]); ?>" />
<?php } ?>
<?php if (trim($regSet["favicon"]) != "") { ?>
<link rel="icon" href="<?php echo RegistrationCore::esc(newsPicFnc(0, $regSet["favicon"])); ?>" />
<?php } ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,500;1,700&display=swap" rel="stylesheet">
<link href="/assets/client/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="/assets/css/registration.css?v=<?php echo time(); ?>" rel="stylesheet">
<link href="/assets/css/registration-scroll.css?v=<?php echo time(); ?>" rel="stylesheet">
<link href="/assets/css/registration-agenda.css?v=<?php echo time(); ?>" rel="stylesheet">

<style>
:root{
	--reg-bg: <?php echo RegistrationCore::esc($regSet["themeBg"]); ?>;
	--reg-surface: <?php echo RegistrationCore::esc($regSet["themeSurface"]); ?>;
	--reg-text: <?php echo RegistrationCore::esc($regSet["themeText"]); ?>;
	--reg-muted: <?php echo RegistrationCore::esc($regSet["themeMuted"]); ?>;
	--reg-border: <?php echo RegistrationCore::esc($regSet["themeBorder"]); ?>;
	--reg-accent: <?php echo RegistrationCore::esc($regSet["themeAccent"]); ?>;
	--reg-accent-text: <?php echo RegistrationCore::esc($regSet["themeAccentText"]); ?>;
	--reg-input-bg: <?php echo RegistrationCore::esc($regSet["themeInputBg"]); ?>;
	--reg-input-text: <?php echo RegistrationCore::esc($regSet["themeInputText"]); ?>;
	--reg-max: <?php echo $regMaxW; ?>px;
	--reg-radius: <?php echo $regRadius; ?>px;
	--reg-title-weight: <?php echo (int)$regSet["themeTitleWeight"]; ?>;
	--reg-body-weight: <?php echo (int)$regSet["themeBodyWeight"]; ?>;
	--reg-title-size: <?php echo $regTitleSize; ?>px;
	--reg-ls: <?php echo (float)$regSet["themeLetterSpacing"]; ?>em;
}
</style>

<?php
/* Дизайнерын өөрийн <head> код (шрифт, analytics, meta г.м) */
if (trim($regSet["customHeadHtml"]) != "") {
	echo $regSet["customHeadHtml"];
}

/* Дизайнерын нэмэлт CSS — хамгийн сүүлд, бүх дүрмийг дарж бичнэ */
if (trim($regSet["customCss"]) != "") {
	echo "\n<style>\n" . str_replace(array("</style", "<script"), array("<\\/style", "&lt;script"), $regSet["customCss"]) . "\n</style>\n";
}

/* Засварлагчийн загвар — нэмэлт CSS-ийн ДАРАА, ингэснээр багаж хэрэгсэл үргэлж харагдана */
if ($regEdit) {
	echo '<link href="/assets/css/registration-edit.css?v=' . time() . '" rel="stylesheet">' . "\n";
	echo '<link href="/assets/css/registration-rte.css?v=' . time() . '" rel="stylesheet">' . "\n";
}
?>
</head>
<body class="reg-body<?php if ($regUpper) echo " reg-upper"; ?><?php if ($regEdit) echo " reg-editing"; ?><?php if ($regHasBg) echo " reg-has-bg"; ?>">

<?php
/* ------------------------------------------------------------------
   Хуудасны ТОГТМОЛ дэвсгэр.

   Энэ давхарга дэлгэцэнд наалдсан (position:fixed) тул хуудсаа доошоо
   гүйлгэхэд зураг/видео нь БАЙРАНДАА үлдэж, зөвхөн текст нь дээгүүр
   нь хөвж өнгөрнө. Засварлагч энэ дээр дарж зураг/видеогоо сольж болно.
   ------------------------------------------------------------------ */

$regBgStyle = "";
if ($regBgPic != "") {
	$regBgStyle .= "background-image:url('" . RegistrationCore::esc(newsPicFnc(0, $regBgPic)) . "');";
}
$regBgStyle .= "background-position:" . RegistrationCore::esc($regPageBg["pos"]) . ";";
if ((int)$regPageBg["blur"] > 0) {
	$regBgStyle .= "filter:blur(" . (int)$regPageBg["blur"] . "px);transform:scale(1.08);";
}
?>
<div class="reg-page-bg" id="regPageBg" data-reg-bg="page"<?php echo RegistrationCore::mediaAttr($regEdit, "setting", 0, "pageBgPic", "both"); ?>>
	<div class="reg-page-bg-media" style="<?php echo $regBgStyle; ?>">
		<?php if ($regBgVideo != "") { ?>
		<video class="reg-page-bg-video" autoplay muted loop playsinline preload="metadata"
			src="<?php echo RegistrationCore::esc(newsPicFnc(0, $regBgVideo)); ?>"></video>
		<?php } ?>
	</div>
	<span class="reg-page-bg-shade" style="opacity:<?php echo (int)$regPageBg["overlay"] / 100; ?>"></span>
</div>

<div class="reg-page" data-reg-snap="<?php echo $regSnap ? "1" : "0"; ?>" data-reg-dots="<?php echo $regDots ? "1" : "0"; ?>">
<?php
if (count($regBlocks) > 0) {
	foreach ($regBlocks as $regBlock) {

		$regData    = $regBlock["data"];
		$regSub     = $regBlock["sub"];
		$regBlockID = (int)$regBlock["blockID"];
		$regHidden  = (int)$regBlock["blockStatus"] !== 1;

		$regTemp = __DIR__ . "/../../pages/registration/blocks/" . $regBlock["blockType"] . ".php";
		if (!is_file($regTemp)) {
			continue;
		}

		/* Засварлах горимд блок бүрийг удирдах бүрхүүлээр ороож өгнө */
		if ($regEdit) {
			$regTypeObj = RegistrationCore::blockTypeObj($regBlock["blockType"]);
			$regTypeLbl = $regTypeObj ? $regTypeObj["label"] : $regBlock["blockType"];
			?>
			<div class="reg-eb<?php if ($regHidden) echo " reg-eb-hidden"; ?>" data-reg-block="<?php echo $regBlockID; ?>">
				<div class="reg-eb-bar">
					<span class="reg-eb-name"><i class="<?php echo RegistrationCore::esc($regTypeObj ? $regTypeObj["icon"] : "fa fa-square-o"); ?>"></i> <?php echo RegistrationCore::esc($regTypeLbl); ?><?php if ($regHidden) echo " — нуусан"; ?></span>
					<span class="reg-eb-tools">
						<button type="button" class="reg-eb-btn" data-op="up" title="Дээш"><i class="fa fa-angle-up"></i></button>
						<button type="button" class="reg-eb-btn" data-op="down" title="Доош"><i class="fa fa-angle-down"></i></button>
						<button type="button" class="reg-eb-btn" data-op="<?php echo $regHidden ? "show" : "hide"; ?>" title="<?php echo $regHidden ? "Харуулах" : "Нуух"; ?>"><i class="fa <?php echo $regHidden ? "fa-eye" : "fa-eye-slash"; ?>"></i></button>
					</span>
				</div>
			<?php
		}

		include $regTemp;

		if ($regEdit) {
			echo "</div>\n";
		}
	}
}

/* Дизайнер формын блокоо санамсаргүй устгасан ч бүртгэл ажиллаж байх ёстой */
if (!$regHasForm) {
	$regData    = RegistrationCore::blockDefaults("form");
	$regSub     = array();
	$regBlockID = 0;
	include __DIR__ . "/../../pages/registration/blocks/form.php";
}
?>
</div>

<?php if (trim($regSet["footerText"]) != "" || $regEdit) { ?>
<footer class="reg-footer">
	<div class="reg-wrap"><span<?php echo RegistrationCore::editAttr($regEdit, "setting", 0, "footerText"); ?>><?php echo RegistrationCore::esc($regSet["footerText"]); ?></span></div>
</footer>
<?php } ?>

<?php if ($regEdit) { ?>
<div id="regEditBar" class="reg-editbar" data-nonce="<?php echo RegistrationCore::esc($regNonce); ?>"
	data-maxbytes="<?php echo (int)RegistrationCore::uploadMaxBytes(); ?>"
	data-maxtext="<?php echo RegistrationCore::esc(RegistrationCore::sizeText(RegistrationCore::uploadMaxBytes())); ?>">
	<span class="reg-editbar-logo">MGL</span>
	<span class="reg-editbar-msg" id="regEditMsg">Текст дээр дарж засна. Зураг дээр дарж солино.</span>
	<span class="reg-editbar-actions">
		<button type="button" class="reg-eb-main" id="regEditSave" disabled>Хадгалах</button>
		<button type="button" class="reg-eb-ghost" id="regEditUndo" disabled>Болих</button>
		<button type="button" class="reg-eb-ghost" id="regEditBg" title="Хуудасны дэвсгэр зураг/видео солих"><i class="fa fa-picture-o"></i> Дэвсгэр</button>
		<a class="reg-eb-ghost" href="/cpadmin/registration/design">CP Admin</a>
		<a class="reg-eb-ghost" href="/registration?editexit=1">Гарах</a>
	</span>
</div>
<input type="file" id="regEditFile" class="reg-hidden-file" accept="image/*"
	aria-hidden="true" tabindex="-1">
<?php } ?>

<script src="/assets/js/registration.js?v=<?php echo time(); ?>"></script>
<script src="/assets/js/registration-scroll.js?v=<?php echo time(); ?>"></script>
<?php if ($regEdit) { ?>
<script src="/assets/js/registration-edit.js?v=<?php echo time(); ?>"></script>
<script src="/assets/js/registration-rte.js?v=<?php echo time(); ?>"></script>
<?php } ?>
</body>
</html>
