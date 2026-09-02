<?php
/** Мэдээллийн хэсэг — огноо / цаг / байршил г.м. Дэд мөр бүр нэг нүд. */

$infoCols = (int)RegistrationCore::val($regData, "columns", "3");
if ($infoCols < 1) {
	$infoCols = 3;
}
?>
<section class="reg-section reg-info" style="<?php echo RegistrationCore::sectionStyle($regData, 80, 80); ?>">
	<div class="reg-wrap">

		<?php if (RegistrationCore::val($regData, "title") != "") { ?>
		<h2 class="reg-section-title"><?php echo RegistrationCore::esc($regData["title"]); ?></h2>
		<?php } ?>

		<?php if (count($regSub) > 0) { ?>
		<div class="reg-info-grid" style="grid-template-columns:repeat(<?php echo $infoCols; ?>, minmax(0,1fr))">
			<?php
			foreach ($regSub as $subObj) {
				$item = $subObj["data"];
				$itemPic  = RegistrationCore::val($item, "pic");
				$itemIcon = RegistrationCore::val($item, "icon");
			?>
			<div class="reg-info-item">
				<?php if ($itemPic != "") { ?>
				<img class="reg-info-pic" alt="" src="<?php echo RegistrationCore::esc(newsPicFnc(0, $itemPic)); ?>">
				<?php } elseif ($itemIcon != "") { ?>
				<i class="reg-info-icon <?php echo RegistrationCore::esc($itemIcon); ?>"></i>
				<?php } ?>

				<?php if (RegistrationCore::val($item, "label") != "") { ?>
				<h3 class="reg-info-label"><?php echo RegistrationCore::esc($item["label"]); ?></h3>
				<?php } ?>

				<?php if (RegistrationCore::val($item, "value") != "") { ?>
				<p class="reg-info-value"><?php echo nl2br(RegistrationCore::esc($item["value"])); ?></p>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

	</div>
</section>
