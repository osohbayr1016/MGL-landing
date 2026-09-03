<?php
/**
 * Hero блок — үндсэн баннер.
 *
 * Дэвсгэр зураг/видео нь ЭНЭ БЛОКТ БАЙХГҮЙ. Хуудас даяар нэг тогтмол
 * дэвсгэр байдаг (skin/new/registration.php доторх .reg-page-bg) тул
 * гүйлгэхэд зураг байрандаа үлдэж, зөвхөн текст нь хөвж өнгөрнө.
 *
 * Засварлагч hero дээр дарвал ТЭР дэвсгэрийг сольж өгнө.
 */

require_once __DIR__ . "/../inc/scroll-section.php";

$heroLogo    = RegistrationCore::val($regData, "logo");
$heroOverlay = (int)RegistrationCore::val($regData, "overlay", "45");
$heroHeight  = RegistrationCore::val($regData, "height", "90");
$heroAlign   = RegistrationCore::val($regData, "align", "center");
$heroValign  = RegistrationCore::val($regData, "valign", "center");
$heroColor   = RegistrationCore::val($regData, "textColor", "#FFFFFF");
$heroBtn     = RegistrationCore::val($regData, "btnText");
$heroID      = $regBlockID;

$heroCls = "reg-hero reg-hero-a-" . RegistrationCore::esc($heroAlign) . " reg-hero-v-" . RegistrationCore::esc($heroValign);
$heroMin = $heroHeight != "auto" ? "min-height:" . (int)$heroHeight . "vh;" : "";
?>
<section class="reg-hero reg-scroll-section <?php echo $heroCls; ?>"
	style="color:<?php echo RegistrationCore::esc($heroColor); ?>;<?php echo $heroMin; ?>"
	<?php echo RegistrationCore::mediaAttr($regEdit, "setting", 0, "pageBgPic", "both"); ?>
	data-reg-bg="page">

	<?php if ($heroOverlay > 0) { ?>
	<span class="reg-hero-overlay" style="opacity:<?php echo max(0, min(100, $heroOverlay)) / 100; ?>"></span>
	<?php } ?>

	<div class="reg-scroll-content reg-hero-inner">
		<div class="reg-wrap">

		<?php if ($heroLogo != "" || $regEdit) { ?>
		<img class="reg-hero-logo<?php if ($heroLogo == "") echo " reg-empty-media"; ?>" alt=""
			style="width:<?php echo (int)RegistrationCore::val($regData, "logoWidth", "160"); ?>px"
			<?php if ($heroLogo != "") { ?>src="<?php echo RegistrationCore::esc(newsPicFnc(0, $heroLogo)); ?>"<?php } ?>
			<?php echo RegistrationCore::mediaAttr($regEdit, "block", $heroID, "logo", "image"); ?>>
		<?php } ?>

		<?php if (RegistrationCore::val($regData, "eyebrow") != "" || $regEdit) { ?>
		<p class="reg-hero-eyebrow"<?php echo RegistrationCore::editAttr($regEdit, "block", $heroID, "eyebrow"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "eyebrow")); ?></p>
		<?php } ?>

		<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
		<h1 class="reg-hero-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $heroID, "title"); ?>><?php echo nl2br(RegistrationCore::esc(RegistrationCore::val($regData, "title"))); ?></h1>
		<?php } ?>

		<?php if (RegistrationCore::val($regData, "subtitle") != "" || $regEdit) { ?>
		<p class="reg-hero-sub"<?php echo RegistrationCore::editAttr($regEdit, "block", $heroID, "subtitle"); ?>><?php echo nl2br(RegistrationCore::esc(RegistrationCore::val($regData, "subtitle"))); ?></p>
		<?php } ?>

		<?php
		$heroDate = RegistrationCore::val($regData, "dateText");
		$heroLoc  = RegistrationCore::val($regData, "locationText");
		if ($heroDate != "" || $heroLoc != "" || $regEdit) {
		?>
		<ul class="reg-hero-meta">
			<?php if ($heroDate != "" || $regEdit) { ?>
			<li><i class="fa fa-calendar"></i> <span<?php echo RegistrationCore::editAttr($regEdit, "block", $heroID, "dateText"); ?>><?php echo RegistrationCore::esc($heroDate); ?></span></li>
			<?php } ?>
			<?php if ($heroLoc != "" || $regEdit) { ?>
			<li><i class="fa fa-map-marker"></i> <span<?php echo RegistrationCore::editAttr($regEdit, "block", $heroID, "locationText"); ?>><?php echo RegistrationCore::esc($heroLoc); ?></span></li>
			<?php } ?>
		</ul>
		<?php } ?>

		<?php if ($heroBtn != "" || $regEdit) { ?>
		<a class="reg-btn reg-hero-btn" href="#registration-form"<?php echo RegistrationCore::editAttr($regEdit, "block", $heroID, "btnText"); ?>><?php echo RegistrationCore::esc($heroBtn); ?></a>
		<?php } ?>

		</div>
	</div>

	<?php if (!$regEdit) { ?>
	<span class="reg-scroll-cue" aria-hidden="true"><i class="fa fa-angle-down"></i></span>
	<?php } ?>
</section>
