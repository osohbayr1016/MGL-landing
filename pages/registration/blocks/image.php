<?php
/** Зургийн блок. */

$imgPic   = RegistrationCore::val($regData, "pic");
$imgWidth = RegistrationCore::val($regData, "width", "container");

if ($imgPic == "" && !$regEdit) {
	return;
}
?>
<section class="reg-section reg-image reg-image-<?php echo RegistrationCore::esc($imgWidth); ?>"
	style="<?php echo RegistrationCore::sectionStyle($regData, 0, 0); ?>">
	<div class="<?php echo $imgWidth == "wide" ? "reg-wrap-full" : "reg-wrap"; ?>">
		<figure class="reg-figure">
			<img class="<?php if ($imgPic == "") echo "reg-empty-media"; ?>"
				alt="<?php echo RegistrationCore::esc(RegistrationCore::val($regData, "caption")); ?>"
				<?php if ($imgPic != "") { ?>src="<?php echo RegistrationCore::esc(newsPicFnc(0, $imgPic)); ?>"<?php } ?>
				<?php echo RegistrationCore::mediaAttr($regEdit, "block", $regBlockID, "pic", "image"); ?>>
			<?php if (RegistrationCore::val($regData, "caption") != "" || $regEdit) { ?>
			<figcaption<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "caption"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "caption")); ?></figcaption>
			<?php } ?>
		</figure>
	</div>
</section>
