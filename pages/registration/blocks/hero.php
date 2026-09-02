<?php
/**
 * Hero блок — дэвсгэр зураг/видео дээрх үндсэн баннер.
 * Утгууд: $regData (CP Admin -> Хуудасны дизайн -> Hero)
 */

$heroPic     = RegistrationCore::val($regData, "bgPic");
$heroVideo   = RegistrationCore::val($regData, "bgVideo");
$heroLogo    = RegistrationCore::val($regData, "logo");
$heroOverlay = (int)RegistrationCore::val($regData, "overlay", "45");
$heroHeight  = RegistrationCore::val($regData, "height", "90");
$heroAlign   = RegistrationCore::val($regData, "align", "center");
$heroValign  = RegistrationCore::val($regData, "valign", "center");
$heroColor   = RegistrationCore::val($regData, "textColor", "#FFFFFF");
$heroBtn     = RegistrationCore::val($regData, "btnText");

$heroStyle = "color:" . RegistrationCore::esc($heroColor) . ";";
if ($heroHeight != "auto") {
	$heroStyle .= "min-height:" . (int)$heroHeight . "vh;";
}
if ($heroPic != "") {
	$heroStyle .= "background-image:url('" . RegistrationCore::esc(newsPicFnc(0, $heroPic)) . "');";
}
?>
<section class="reg-hero reg-hero-a-<?php echo RegistrationCore::esc($heroAlign); ?> reg-hero-v-<?php echo RegistrationCore::esc($heroValign); ?>"
	style="<?php echo $heroStyle; ?>">

	<?php if ($heroVideo != "") { ?>
	<video class="reg-hero-video" autoplay muted loop playsinline
		src="<?php echo RegistrationCore::esc(newsPicFnc(0, $heroVideo)); ?>"></video>
	<?php } ?>

	<span class="reg-hero-overlay" style="opacity:<?php echo max(0, min(100, $heroOverlay)) / 100; ?>"></span>

	<div class="reg-wrap reg-hero-inner">

		<?php if ($heroLogo != "") { ?>
		<img class="reg-hero-logo" alt=""
			style="width:<?php echo (int)RegistrationCore::val($regData, "logoWidth", "160"); ?>px"
			src="<?php echo RegistrationCore::esc(newsPicFnc(0, $heroLogo)); ?>">
		<?php } ?>

		<?php if (RegistrationCore::val($regData, "eyebrow") != "") { ?>
		<p class="reg-hero-eyebrow"><?php echo RegistrationCore::esc($regData["eyebrow"]); ?></p>
		<?php } ?>

		<?php if (RegistrationCore::val($regData, "title") != "") { ?>
		<h1 class="reg-hero-title"><?php echo nl2br(RegistrationCore::esc($regData["title"])); ?></h1>
		<?php } ?>

		<?php if (RegistrationCore::val($regData, "subtitle") != "") { ?>
		<p class="reg-hero-sub"><?php echo nl2br(RegistrationCore::esc($regData["subtitle"])); ?></p>
		<?php } ?>

		<?php
		$heroDate = RegistrationCore::val($regData, "dateText");
		$heroLoc  = RegistrationCore::val($regData, "locationText");
		if ($heroDate != "" || $heroLoc != "") {
		?>
		<ul class="reg-hero-meta">
			<?php if ($heroDate != "") { ?>
			<li><i class="fa fa-calendar"></i> <?php echo RegistrationCore::esc($heroDate); ?></li>
			<?php } ?>
			<?php if ($heroLoc != "") { ?>
			<li><i class="fa fa-map-marker"></i> <?php echo RegistrationCore::esc($heroLoc); ?></li>
			<?php } ?>
		</ul>
		<?php } ?>

		<?php if ($heroBtn != "") { ?>
		<a class="reg-btn reg-hero-btn" href="#registration-form"><?php echo RegistrationCore::esc($heroBtn); ?></a>
		<?php } ?>

	</div>
</section>
