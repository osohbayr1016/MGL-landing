<?php
/** Хөтөлбөр (agenda) — огноо / цаг / байршил + rich text. */

require_once __DIR__ . "/../inc/scroll-section.php";

regScrollOpen("reg-section reg-agenda", $regData, 80, 80);
?>
	<div class="reg-wrap">

		<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
		<h2 class="reg-section-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "title"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "title")); ?></h2>
		<?php } ?>

		<?php if (count($regSub) > 0) { ?>
		<div class="reg-agenda-list">
			<?php foreach ($regSub as $subObj) {
				$item   = $subObj["data"];
				$itemID = (int)$subObj["blockID"];
				$hasMeta = RegistrationCore::val($item, "date") != "" || RegistrationCore::val($item, "time") != ""
					|| RegistrationCore::val($item, "location") != "" || $regEdit;
				$hasBody = RegistrationCore::val($item, "body") != "" || $regEdit;
				if (!$hasMeta && !$hasBody && !$regEdit) {
					continue;
				}
			?>
			<article class="reg-agenda-item">
				<?php if ($hasMeta) { ?>
				<div class="reg-agenda-meta">
					<div class="reg-agenda-field reg-agenda-date">
						<span class="reg-agenda-label">Огноо</span>
						<span class="reg-agenda-value"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "date"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($item, "date")); ?></span>
					</div>
					<div class="reg-agenda-field reg-agenda-time">
						<span class="reg-agenda-label">Цаг</span>
						<span class="reg-agenda-value"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "time"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($item, "time")); ?></span>
					</div>
					<div class="reg-agenda-field reg-agenda-location">
						<span class="reg-agenda-label">Байршил</span>
						<span class="reg-agenda-value"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "location"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($item, "location")); ?></span>
					</div>
				</div>
				<?php } ?>

				<?php if ($hasBody) { ?>
				<div class="reg-agenda-body reg-rte"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "body", "html"); ?>><?php echo RegistrationCore::val($item, "body"); ?></div>
				<?php } ?>
			</article>
			<?php } ?>
		</div>
		<?php } ?>

	</div>
<?php regScrollClose(); ?>
