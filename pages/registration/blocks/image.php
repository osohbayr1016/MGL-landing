<?php
/** Зургийн блок. */

$imgPic   = RegistrationCore::val($regData, "pic");
$imgWidth = RegistrationCore::val($regData, "width", "container");

if ($imgPic == "") {
	return;
}
?>
<section class="reg-section reg-image reg-image-<?php echo RegistrationCore::esc($imgWidth); ?>"
	style="<?php echo RegistrationCore::sectionStyle($regData, 0, 0); ?>">
	<div class="<?php echo $imgWidth == "wide" ? "reg-wrap-full" : "reg-wrap"; ?>">
		<figure class="reg-figure">
			<img alt="<?php echo RegistrationCore::esc(RegistrationCore::val($regData, "caption")); ?>"
				src="<?php echo RegistrationCore::esc(newsPicFnc(0, $imgPic)); ?>">
			<?php if (RegistrationCore::val($regData, "caption") != "") { ?>
			<figcaption><?php echo RegistrationCore::esc($regData["caption"]); ?></figcaption>
			<?php } ?>
		</figure>
	</div>
</section>
