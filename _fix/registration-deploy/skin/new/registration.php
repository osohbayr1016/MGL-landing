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
 */

$regUpper  = ((string)$regSet["themeUppercase"] === "1");
$regMaxW   = (int)$regSet["themeMaxWidth"] > 0 ? (int)$regSet["themeMaxWidth"] : 1080;
$regRadius = (int)$regSet["themeRadius"];
$regTitleSize = (int)$regSet["themeTitleSize"] > 0 ? (int)$regSet["themeTitleSize"] : 56;
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
<link href="/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="/assets/css/registration.css?v=<?php echo time(); ?>" rel="stylesheet">

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
?>
</head>
<body class="reg-body<?php if ($regUpper) echo " reg-upper"; ?>">

<div class="reg-page">
<?php
if (count($regBlocks) > 0) {
	foreach ($regBlocks as $regBlock) {

		$regData = $regBlock["data"];
		$regSub  = $regBlock["sub"];

		$regTemp = __DIR__ . "/../../pages/registration/blocks/" . $regBlock["blockType"] . ".php";
		if (!is_file($regTemp)) {
			continue;
		}

		include $regTemp;
	}
}

/* Дизайнер формын блокоо санамсаргүй устгасан ч бүртгэл ажиллаж байх ёстой */
if (!$regHasForm) {
	$regData = RegistrationCore::blockDefaults("form");
	$regSub  = array();
	include __DIR__ . "/../../pages/registration/blocks/form.php";
}
?>
</div>

<?php if (trim($regSet["footerText"]) != "") { ?>
<footer class="reg-footer">
	<div class="reg-wrap"><?php echo RegistrationCore::esc($regSet["footerText"]); ?></div>
</footer>
<?php } ?>

<script src="/assets/js/registration.js?v=<?php echo time(); ?>"></script>
</body>
</html>
