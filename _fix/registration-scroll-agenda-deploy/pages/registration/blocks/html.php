<?php
/** Чөлөөт HTML блок. */

require_once __DIR__ . "/../inc/scroll-section.php";

if (RegistrationCore::val($regData, "body") == "") {
	return;
}

regScrollOpen("reg-section reg-html", $regData, 0, 0);
?>
	<?php echo $regData["body"]; ?>
<?php regScrollClose(); ?>
