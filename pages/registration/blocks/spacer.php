<?php
/** Хоосон зай / тусгаарлагч зураас. */

$spHeight = (int)RegistrationCore::val($regData, "height", "60");
$spBg     = RegistrationCore::val($regData, "bg");
$spLine   = RegistrationCore::val($regData, "line") == "y";

$spStyle = "height:" . $spHeight . "px;";
if ($spBg != "") {
	$spStyle .= "background-color:" . RegistrationCore::esc($spBg) . ";";
}
?>
<div class="reg-spacer" style="<?php echo $spStyle; ?>">
	<?php if ($spLine) { ?>
	<span class="reg-spacer-line"></span>
	<?php } ?>
</div>
