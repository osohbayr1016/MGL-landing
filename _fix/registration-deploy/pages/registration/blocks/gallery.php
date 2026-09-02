<?php
/** Зургийн цомог — дэд мөр бүр нэг зураг. */

$galCols = (int)RegistrationCore::val($regData, "columns", "3");
if ($galCols < 1) {
	$galCols = 3;
}
$galGap = (int)RegistrationCore::val($regData, "gap", "16");
?>
<section class="reg-section reg-gallery" style="<?php echo RegistrationCore::sectionStyle($regData, 80, 80); ?>">
	<div class="reg-wrap">

		<?php if (RegistrationCore::val($regData, "title") != "") { ?>
		<h2 class="reg-section-title"><?php echo RegistrationCore::esc($regData["title"]); ?></h2>
		<?php } ?>

		<?php if (count($regSub) > 0) { ?>
		<div class="reg-gallery-grid"
			style="grid-template-columns:repeat(<?php echo $galCols; ?>, minmax(0,1fr));gap:<?php echo $galGap; ?>px">
			<?php
			foreach ($regSub as $subObj) {
				$item = $subObj["data"];
				if (RegistrationCore::val($item, "pic") == "") {
					continue;
				}
			?>
			<figure class="reg-gallery-item">
				<img alt="<?php echo RegistrationCore::esc(RegistrationCore::val($item, "caption")); ?>"
					src="<?php echo RegistrationCore::esc(newsPicFnc(0, $item["pic"])); ?>">
				<?php if (RegistrationCore::val($item, "caption") != "") { ?>
				<figcaption><?php echo RegistrationCore::esc($item["caption"]); ?></figcaption>
				<?php } ?>
			</figure>
			<?php } ?>
		</div>
		<?php } ?>

	</div>
</section>
