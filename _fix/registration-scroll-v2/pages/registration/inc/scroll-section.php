<?php
/**
 * Section scroll wrapper — дэвсгөр fixed, текст slide animation.
 * regScrollOpen() / regScrollClose() блок template-үүдэд ашиглана.
 */

function regScrollPadStyle($data, $defTop = 0, $defBottom = 0)
{
	$top = (int)RegistrationCore::val($data, "padTop", $defTop);
	$bot = (int)RegistrationCore::val($data, "padBottom", $defBottom);

	return "padding-top:" . $top . "px;padding-bottom:" . $bot . "px;";
}

function regScrollBgStyle($data)
{
	$bg = RegistrationCore::val($data, "bg");

	return $bg != "" ? "background-color:" . RegistrationCore::esc($bg) . ";" : "";
}

function regScrollOpen($classes, $data, $defTop = 0, $defBottom = 0, $extra = "")
{
	$cls = "reg-scroll-section " . trim($classes);
	if ($extra != "") {
		$cls .= " " . trim($extra);
	}

	echo '<section class="' . RegistrationCore::esc($cls) . '">';
	echo '<div class="reg-section-bg" style="' . regScrollBgStyle($data) . '"></div>';
	echo '<div class="reg-scroll-content" style="' . regScrollPadStyle($data, $defTop, $defBottom) . '">';
}

function regScrollClose()
{
	echo '</div></section>';
}
