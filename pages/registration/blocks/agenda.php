<?php
/**
 * Хөтөлбөр (agenda) блок — хоёр давхаргатай.
 *
 *   1. TIMELINE — цаг нь зүүн талд, дэргэд нь босоо шугам. Мөр бүр нь
 *      CP Admin-ы "дэд мөр" (цаг / огноо / байршил / агуулга).
 *
 *   2. ХӨТӨЛБӨР — тусдаа чөлөөт хэсэг. Хуудсан дээрээ шууд дарж бичихэд
 *      үсгийн хэмжээ, өнгө, загвар, зэрэгцүүлэлт өөрчлөх багаж гарч ирнэ.
 *      Хайрцгийн өргөн, байрлал, дэвсгэрийг нь мөн тэндээс тохируулна.
 */

require_once __DIR__ . "/../inc/scroll-section.php";

/* ---- Доод "Хөтөлбөр" хайрцгийн тохиргоо ---- */

$agProgram = RegistrationCore::val($regData, "program");
$agProgTtl = RegistrationCore::val($regData, "programTitle", "Хөтөлбөр");
$agAlign   = RegistrationCore::val($regData, "programAlign", "left");
$agPos     = RegistrationCore::val($regData, "programPos", "center");
$agWidth   = (int)RegistrationCore::val($regData, "programWidth", "760");
$agBg      = RegistrationCore::val($regData, "programBg");
$agOpacity = (int)RegistrationCore::val($regData, "programOpacity", "70");
$agPad     = (int)RegistrationCore::val($regData, "programPad", "32");

if (!in_array($agAlign, array("left", "center", "right"))) {
	$agAlign = "left";
}
if (!in_array($agPos, array("left", "center", "right"))) {
	$agPos = "center";
}

$agFill = RegistrationCore::rgba($agBg, $agOpacity);

$agStyle  = "max-width:" . ($agWidth > 0 ? $agWidth : 760) . "px;";
$agStyle .= "text-align:" . $agAlign . ";";
$agStyle .= "padding:" . max(0, $agPad) . "px;";
$agStyle .= $agPos == "center" ? "margin-left:auto;margin-right:auto;"
	: ($agPos == "right" ? "margin-left:auto;margin-right:0;" : "margin-left:0;margin-right:auto;");
if ($agFill != "") {
	$agStyle .= "background-color:" . $agFill . ";";
}

/* Агуулга бичээгүй бол зочдод харуулахгүй, засварлагчид үргэлж харагдана */
$agShow = (trim(strip_tags($agProgram)) != "" || $regEdit);

regScrollOpen("reg-section reg-agenda", $regData, 80, 80);
?>
	<div class="reg-wrap">

		<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
		<h2 class="reg-section-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "title"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "title")); ?></h2>
		<?php } ?>

		<?php if (count($regSub) > 0) { ?>
		<ol class="reg-timeline">
			<?php foreach ($regSub as $subObj) {
				$item   = $subObj["data"];
				$itemID = (int)$subObj["blockID"];

				$itTime = RegistrationCore::val($item, "time");
				$itDate = RegistrationCore::val($item, "date");
				$itLoc  = RegistrationCore::val($item, "location");
				$itBody = RegistrationCore::val($item, "body");

				if (!$regEdit && $itTime == "" && $itDate == "" && $itLoc == "" && trim(strip_tags($itBody)) == "") {
					continue;
				}
			?>
			<li class="reg-tl-item">
				<span class="reg-tl-mark" aria-hidden="true"></span>

				<div class="reg-tl-when">
					<?php if ($itTime != "" || $regEdit) { ?>
					<span class="reg-tl-time"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "time"); ?>><?php echo RegistrationCore::esc($itTime); ?></span>
					<?php } ?>
					<?php if ($itDate != "" || $regEdit) { ?>
					<span class="reg-tl-date"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "date"); ?>><?php echo RegistrationCore::esc($itDate); ?></span>
					<?php } ?>
				</div>

				<div class="reg-tl-body">
					<?php if ($itBody != "" || $regEdit) { ?>
					<div class="reg-tl-text reg-rte"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "body", "html"); ?>><?php echo $itBody; ?></div>
					<?php } ?>

					<?php if ($itLoc != "" || $regEdit) { ?>
					<span class="reg-tl-loc"><i class="fa fa-map-marker"></i> <span<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "location"); ?>><?php echo RegistrationCore::esc($itLoc); ?></span></span>
					<?php } ?>
				</div>
			</li>
			<?php } ?>
		</ol>
		<?php } ?>

		<?php if ($agShow) { ?>
		<div class="reg-program<?php if ($agFill != "") echo " reg-program-filled"; ?>"
			style="<?php echo $agStyle; ?>"
			<?php if ($regEdit) { ?>data-reg-panel="<?php echo $regBlockID; ?>"
			data-align="<?php echo RegistrationCore::esc($agAlign); ?>"
			data-pos="<?php echo RegistrationCore::esc($agPos); ?>"
			data-width="<?php echo $agWidth; ?>"
			data-bg="<?php echo RegistrationCore::esc($agBg); ?>"
			data-opacity="<?php echo $agOpacity; ?>"
			data-pad="<?php echo $agPad; ?>"<?php } ?>>

			<?php if ($agProgTtl != "" || $regEdit) { ?>
			<h3 class="reg-program-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "programTitle"); ?>><?php echo RegistrationCore::esc($agProgTtl); ?></h3>
			<?php } ?>

			<div class="reg-program-body reg-rte"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "program", "html"); ?>><?php echo $agProgram; ?></div>
		</div>
		<?php } ?>

	</div>
<?php regScrollClose(); ?>
