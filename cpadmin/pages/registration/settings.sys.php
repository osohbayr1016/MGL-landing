<?php
/** Арга хэмжээ, багтаамж, хугацаа, мессежийн тохиргоо. */

$widJsArr["regSettings"] = $clkMenuModDir . "settings.js.php";
$incPageUrl              = $clkMenuModDir . "settings.php";

$regSet      = RegistrationCore::settings($db);
$regAllCount = RegistrationCore::entryCount($db);
$regStatus   = RegistrationCore::status($db, $regSet);

$regPageLink = RegistrationCore::pageUrl($regSet);

/** datetime-local input-д тохирох формат руу */
function regDtLocal($value)
{
	$value = trim((string)$value);
	if ($value == "") {
		return "";
	}

	$stamp = strtotime(str_replace("T", " ", $value));

	return $stamp ? date("Y-m-d\TH:i", $stamp) : "";
}
