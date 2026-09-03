<?php
/** Зургийн цомог — дэд мөр бүр нэг зураг. */

require_once __DIR__ . "/../inc/scroll-section.php";

$galCols = (int)RegistrationCore::val($regData, "columns", "3");
if ($galCols < 1) {
	$galCols = 3;
}
$galGap = (int)RegistrationCore::val($regData, "gap", "16");

regScrollOpen("reg-section reg-gallery", $regData, 80, 80);
?>
	<div class="reg-wrap">

		<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
		<h2 class="reg-section-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "title"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "title")); ?></h2>
		<?php } ?>

		<?php if (count($regSub) > 0) { ?>
		<div class="reg-gallery-grid"
			style="grid-template-columns:repeat(<?php echo $galCols; ?>, minmax(0,1fr));gap:<?php echo $galGap; ?>px">
			<?php
			foreach ($regSub as $subObj) {
				$item   = $subObj["data"];
				$itemID = (int)$subObj["blockID"];
				$itemPic = RegistrationCore::val($item, "pic");

				if ($itemPic == "" && !$regEdit) {
					continue;
				}
			?>
			<figure class="reg-gallery-item">
				<img class="<?php if ($itemPic == "") echo "reg-empty-media"; ?>"
					alt="<?php echo RegistrationCore::esc(RegistrationCore::val($item, "caption")); ?>"
					<?php if ($itemPic != "") { ?>src="<?php echo RegistrationCore::esc(newsPicFnc(0, $itemPic)); ?>"<?php } ?>
					<?php echo RegistrationCore::mediaAttr($regEdit, "block", $itemID, "pic", "image"); ?>>
				<?php if (RegistrationCore::val($item, "caption") != "" || $regEdit) { ?>
				<figcaption<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "caption"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($item, "caption")); ?></figcaption>
				<?php } ?>
			</figure>
			<?php } ?>
		</div>
		<?php } ?>

	</div>
<?php regScrollClose(); ?>
