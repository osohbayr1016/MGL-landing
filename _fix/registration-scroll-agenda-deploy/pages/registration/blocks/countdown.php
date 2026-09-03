<?php
/** Countdown блок. */

require_once __DIR__ . "/../inc/scroll-section.php";

$cdTarget = RegistrationCore::val($regData, "target");
if ($cdTarget == "") {
	$cdTarget = $regSet["eventDate"];
}

$cdStamp = $cdTarget != "" ? strtotime(str_replace("T", " ", $cdTarget)) : 0;
if (!$cdStamp && !$regEdit) {
	return;
}

regScrollOpen("reg-section reg-countdown", $regData, 60, 60);
?>
	<div class="reg-wrap">

		<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
		<h2 class="reg-section-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "title"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "title")); ?></h2>
		<?php } ?>

		<?php if ($cdStamp) { ?>
		<div class="reg-countdown-grid" data-reg-countdown="<?php echo (int)$cdStamp; ?>">
			<div class="reg-cd-cell"><span class="reg-cd-num" data-cd="d">--</span><span class="reg-cd-lbl">хоног</span></div>
			<div class="reg-cd-cell"><span class="reg-cd-num" data-cd="h">--</span><span class="reg-cd-lbl">цаг</span></div>
			<div class="reg-cd-cell"><span class="reg-cd-num" data-cd="m">--</span><span class="reg-cd-lbl">минут</span></div>
			<div class="reg-cd-cell"><span class="reg-cd-num" data-cd="s">--</span><span class="reg-cd-lbl">секунд</span></div>
		</div>
		<?php } else { ?>
		<p class="reg-edit-hint">Огноо оруулаагүй байна — CP Admin -> Тохиргоо хэсгээс оруулна уу.</p>
		<?php } ?>

	</div>
<?php regScrollClose(); ?>
