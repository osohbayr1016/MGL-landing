<?php
/**
 * Хөтөлбөр (agenda) блок — хоёр давхаргатай.
 *
 *   1. TIMELINE — цаг нь зүүн талд, дэргэд нь босоо шугам. Мөр бүрд
 *      ЦАГ / ОГНОО / БАЙРШИЛ / ТАЙЛБАР гэсэн тусдаа талбарууд байна.
 *      Засварлах горимд мөр нэмэх, устгах, дээш/доош зөөх боломжтой.
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

/* ---- Coming soon ----
   Асаалттай үед хөтөлбөрийн ОРОНД "тун удахгүй" дэлгэц харагдана.
   Бичсэн хөтөлбөр устдаггүй — унтраамагц буцаж ирнэ. */
$agSoon      = RegistrationCore::val($regData, "soon") == "y";
$agSoonTitle = RegistrationCore::val($regData, "soonTitle", "Тун удахгүй");
$agSoonText  = RegistrationCore::val($regData, "soonText", "Өдөрлөгийн дэлгэрэнгүй хөтөлбөрийг удахгүй энд нийтэлнэ.");

regScrollOpen("reg-section reg-agenda", $regData, 80, 80);
?>
	<div class="reg-wrap">

		<?php if ($regEdit) { ?>
		<div class="reg-soon-switch">
			<label class="reg-soon-sw">
				<input type="checkbox" data-reg-soon="<?php echo $regBlockID; ?>"<?php if ($agSoon) echo " checked"; ?>>
				<span class="reg-soon-track"><span class="reg-soon-knob"></span></span>
				<span class="reg-soon-lbl">Coming soon</span>
			</label>
			<span class="reg-soon-note"><?php echo $agSoon
				? "Хөтөлбөр түр нуугдсан. Унтраавал шууд буцаж ирнэ."
				: "Асаавал хөтөлбөрийн оронд \"тун удахгүй\" дэлгэц гарна."; ?></span>
		</div>
		<?php } ?>

		<?php if ($agSoon) { ?>
		<div class="reg-soon">
			<span class="reg-soon-badge">Coming soon</span>
			<h2 class="reg-soon-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "soonTitle"); ?>><?php echo RegistrationCore::esc($agSoonTitle); ?></h2>
			<?php if ($agSoonText != "" || $regEdit) { ?>
			<p class="reg-soon-text"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "soonText"); ?>><?php echo nl2br(RegistrationCore::esc($agSoonText)); ?></p>
			<?php } ?>
			<span class="reg-soon-dots" aria-hidden="true"><span></span><span></span><span></span></span>
		</div>
		<?php } else { ?>

		<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
		<h2 class="reg-section-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "title"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "title")); ?></h2>
		<?php } ?>

		<?php if (count($regSub) > 0 || $regEdit) { ?>
		<ol class="reg-timeline<?php if ($regEdit) echo " reg-timeline-edit"; ?>">
			<?php
			$agRows = 0;
			foreach ($regSub as $subObj) {
				$item   = $subObj["data"];
				$itemID = (int)$subObj["blockID"];

				$itTime = RegistrationCore::val($item, "time");
				$itDate = RegistrationCore::val($item, "date");
				$itLoc  = RegistrationCore::val($item, "location");
				$itBody = RegistrationCore::val($item, "body");

				if (!$regEdit && $itTime == "" && $itDate == "" && $itLoc == "" && trim(strip_tags($itBody)) == "") {
					continue;
				}

				$agRows++;
			?>
			<li class="reg-tl-item">
				<span class="reg-tl-mark" aria-hidden="true"></span>

				<div class="reg-tl-when">
					<?php if ($itTime != "" || $regEdit) { ?>
					<span class="reg-tl-time" data-reg-ph="Цаг"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "time"); ?>><?php echo RegistrationCore::esc($itTime); ?></span>
					<?php } ?>
					<?php if ($itDate != "" || $regEdit) { ?>
					<span class="reg-tl-date" data-reg-ph="Огноо"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "date"); ?>><?php echo RegistrationCore::esc($itDate); ?></span>
					<?php } ?>
				</div>

				<div class="reg-tl-body">
					<?php if ($itBody != "" || $regEdit) { ?>
					<div class="reg-tl-text reg-rte" data-reg-ph="Тайлбар"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "body", "html"); ?>><?php echo $itBody; ?></div>
					<?php } ?>

					<?php if ($itLoc != "" || $regEdit) { ?>
					<span class="reg-tl-loc"><i class="fa fa-map-marker"></i> <span data-reg-ph="Байршил"<?php echo RegistrationCore::editAttr($regEdit, "block", $itemID, "location"); ?>><?php echo RegistrationCore::esc($itLoc); ?></span></span>
					<?php } ?>
				</div>

				<?php if ($regEdit) { ?>
				<span class="reg-tl-tools">
					<button type="button" class="reg-tl-btn" data-reg-subop="subup" data-reg-sub="<?php echo $itemID; ?>" title="Дээш"><i class="fa fa-angle-up"></i></button>
					<button type="button" class="reg-tl-btn" data-reg-subop="subdown" data-reg-sub="<?php echo $itemID; ?>" title="Доош"><i class="fa fa-angle-down"></i></button>
					<button type="button" class="reg-tl-btn reg-tl-del" data-reg-subop="subdel" data-reg-sub="<?php echo $itemID; ?>" title="Энэ мөрийг устгах"><i class="fa fa-trash"></i></button>
				</span>
				<?php } ?>
			</li>
			<?php } ?>

			<?php if ($regEdit) { ?>
			<li class="reg-tl-add">
				<button type="button" class="reg-tl-addbtn" data-reg-subop="subadd" data-reg-sub="<?php echo $regBlockID; ?>">
					<i class="fa fa-plus"></i> Хөтөлбөрийн мөр нэмэх
				</button>
				<?php if ($agRows < 1) { ?>
				<span class="reg-tl-hint">Мөр бүрд цаг, огноо, байршил, тайлбарыг тусад нь бичнэ.</span>
				<?php } ?>
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

			<div class="reg-program-body reg-rte" data-reg-ph="Нэмэлт тайлбар"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "program", "html"); ?>><?php echo $agProgram; ?></div>
		</div>
		<?php } ?>

		<?php } /* /Coming soon */ ?>

	</div>
<?php regScrollClose(); ?>
