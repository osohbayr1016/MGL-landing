<?php
/**
 * Builds page.html: a minimal "about" page (the site's real CSS) whose first
 * section is rendered through the real widgets/pagesch/floorplan.hero.php.
 * No database needed (FloorPlanCore::load(null) returns the seeded defaults).
 *
 *   php tests/floorplan/e2e/render.php
 */
$root = realpath(__DIR__ . "/../../..");
require $root . "/class/floorplan.class.php";

$fpWidHtml = '<div class="wrapper">
	<div class="pageHeader" id="widhas1166">
		<div class="header-bt wrapper">
			<h1>Профайл</h1>
			 
		</div>
		<picture>
		<source type="image/jpeg" srcset="/assets/images/floorplan/office-20f-2560.webp">
		<img src="/assets/images/floorplan/office-20f-2560.webp">
		</picture>
	</div>
	<div class="aboutText"><ul><li>Бид Монгол Улсынхаа барилга бүтээн байгуулалтын салбарын хөгжлийг дэлхийн стандартад нийцсэн чанартай ... </li></ul></div>
</div>';

$fpData = FloorPlanCore::load(null, true);
include $root . "/widgets/pagesch/floorplan.hero.php";

$page = '<!DOCTYPE html><html lang="mn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">'
	. '<title>about (floorplan e2e)</title>'
	. '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;500;600;700;800&display=swap" rel="stylesheet">'
	. '<link href="/assets/css/style.css" rel="stylesheet"><link href="/assets/css/mobile.css" rel="stylesheet"></head>'
	. '<body class="about page-header-default">'
	. '<div class="mainNavHeader"><div id="header-top"><div class="header-logo-div"><a class="header-logo" href="/">MGL E&amp;C</a></div></div></div>'
	. '<div class="headersubmenu"><ul class="aboutsubmenu"><li><a class="active" href="#widhas1166"><span class="hover-text">Профайл</span></a></li><li><a href="#x"><span class="hover-text">Ажил эрхлэлт</span></a></li></ul></div>'
	. $fpFinalHtml
	. '<div class="wrapper"><p style="height:1600px">more page content</p></div>'
	. '<input id="e2e-input" type="text" style="position:fixed;left:-999px;top:0">'
	. '</body></html>';

file_put_contents(__DIR__ . "/page.html", $page);
echo "page.html: " . strlen($page) . " bytes\n";
