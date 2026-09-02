<?php
/** Текст блок — editor-оор бичсэн чөлөөт агуулга. */

$textAlign = RegistrationCore::val($regData, "align", "left");
$textMax   = (int)RegistrationCore::val($regData, "maxWidth", "760");
?>
<section class="reg-section reg-text reg-align-<?php echo RegistrationCore::esc($textAlign); ?>"
	style="<?php echo RegistrationCore::sectionStyle($regData, 80, 80); ?>">
	<div class="reg-wrap">
		<div class="reg-text-inner" style="max-width:<?php echo $textMax > 0 ? $textMax : 760; ?>px">

			<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
			<h2 class="reg-section-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "title"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "title")); ?></h2>
			<?php } ?>

			<?php if (RegistrationCore::val($regData, "body") != "" || $regEdit) { ?>
			<div class="reg-rte"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "body", "html"); ?>><?php echo RegistrationCore::val($regData, "body"); ?></div>
			<?php } ?>

		</div>
	</div>
</section>
