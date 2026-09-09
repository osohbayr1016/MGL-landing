<?php
/**
 * Өргөдлийн форм.
 *
 * Талбарууд CP Admin -> Дадлага -> "Формын талбар" хэсгээс ирнэ
 * ($applyFields), тул энд ямар ч талбар hardcode хийгээгүй.
 *
 * Шаардах хувьсагчид: $applyType, $applySet, $applyFields, $applyStatus,
 *                     $applyErrors, $applyValues, $applyDone, $applyAction
 */

$applyMaxText = ApplyCore::sizeText(ApplyCore::maxFileBytes($applySet));
$applyExtArr  = array_keys(ApplyCore::allowedExt($applySet));
$applyExtText = implode(", ", $applyExtArr);
$applyAccept  = "." . implode(",.", $applyExtArr);
?>
<section class="apply-section" id="apply-form">
	<div class="apply-wrap">

		<div class="apply-head">
			<h2 class="apply-title"><?php echo ApplyCore::esc(ApplyCore::pageTitle($applySet, $applyType)); ?></h2>
			<?php if (ApplyCore::pageText($applySet, $applyType) != "") { ?>
			<p class="apply-lead"><?php echo nl2br(ApplyCore::esc(ApplyCore::pageText($applySet, $applyType))); ?></p>
			<?php } ?>
		</div>

		<div class="apply-box">

		<?php if ($applyDone) { ?>

			<div class="apply-state apply-state--ok">
				<h3><?php echo ApplyCore::esc($applySet["successTitle"]); ?></h3>
				<p><?php echo nl2br(ApplyCore::esc($applySet["successText"])); ?></p>
			</div>

		<?php } elseif (!$applyStatus["open"]) { ?>

			<div class="apply-state apply-state--closed">
				<h3><?php echo ApplyCore::esc($applyStatus["title"]); ?></h3>
				<p><?php echo nl2br(ApplyCore::esc($applyStatus["text"])); ?></p>
			</div>

		<?php } else { ?>

			<?php if (isset($applyErrors["_form"])) { ?>
			<div class="apply-alert"><?php echo ApplyCore::esc($applyErrors["_form"]); ?></div>
			<?php } ?>

			<form class="apply-form" method="post" action="<?php echo ApplyCore::esc($applyAction); ?>"
				enctype="multipart/form-data" novalidate>

				<div class="apply-grid">
				<?php
				foreach ($applyFields as $field) {

					$key     = $field["fieldKey"];
					$type    = $field["fieldType"];
					$width   = $field["fieldWidth"] != "" ? $field["fieldWidth"] : "full";
					$req     = (int)$field["fieldRequired"] == 1;
					$err     = isset($applyErrors[$key]) ? $applyErrors[$key] : "";
					$val     = isset($applyValues[$key]) ? $applyValues[$key] : "";
					$ph      = $field["fieldPlaceholder"];
					$inputID = "applyf_" . preg_replace('/[^A-Za-z0-9_]/', "", $key);
					$options = ApplyCore::fieldHasOptions($type)
						? ApplyCore::parseOptions($field["fieldOptions"])
						: array();
				?>
				<div class="apply-field apply-w-<?php echo ApplyCore::esc($width); ?><?php if ($err != "") echo " apply-has-error"; ?>">

					<?php if ($type != "consent") { ?>
					<label class="apply-label" for="<?php echo $inputID; ?>">
						<?php echo ApplyCore::esc($field["fieldLabel"]); ?><?php if ($req) { ?><span class="apply-req">*</span><?php } ?>
					</label>
					<?php } ?>

					<?php
					switch ($type) {

						case "textarea":
					?>
						<textarea class="apply-input" id="<?php echo $inputID; ?>" name="<?php echo ApplyCore::esc($key); ?>"
							rows="5" placeholder="<?php echo ApplyCore::esc($ph); ?>"<?php if ($req) echo ' required'; ?>><?php echo ApplyCore::esc(is_array($val) ? "" : $val); ?></textarea>
					<?php
						break;

						case "file":
					?>
						<input class="apply-file" type="file" id="<?php echo $inputID; ?>"
							name="<?php echo ApplyCore::esc($key); ?>"
							accept="<?php echo ApplyCore::esc($applyAccept); ?>"<?php if ($req) echo ' required'; ?>>
						<span class="apply-help"><?php echo ApplyCore::esc($applyExtText); ?> — дээд тал нь <?php echo ApplyCore::esc($applyMaxText); ?></span>
					<?php
						break;

						case "select":
					?>
						<select class="apply-input" id="<?php echo $inputID; ?>" name="<?php echo ApplyCore::esc($key); ?>"<?php if ($req) echo ' required'; ?>>
							<option value=""><?php echo $ph != "" ? ApplyCore::esc($ph) : "— сонгоно уу —"; ?></option>
							<?php foreach ($options as $optVal => $optLabel) { ?>
							<option value="<?php echo ApplyCore::esc($optVal); ?>"<?php if ((string)$val === (string)$optVal) echo ' selected'; ?>><?php echo ApplyCore::esc($optLabel); ?></option>
							<?php } ?>
						</select>
					<?php
						break;

						case "radio":
					?>
						<div class="apply-choice-list">
						<?php $ri = 0; foreach ($options as $optVal => $optLabel) { $ri++; ?>
							<label class="apply-choice" for="<?php echo $inputID . "_" . $ri; ?>">
								<input type="radio" id="<?php echo $inputID . "_" . $ri; ?>"
									name="<?php echo ApplyCore::esc($key); ?>"
									value="<?php echo ApplyCore::esc($optVal); ?>"<?php if ((string)$val === (string)$optVal) echo ' checked'; ?>>
								<span><?php echo ApplyCore::esc($optLabel); ?></span>
							</label>
						<?php } ?>
						</div>
					<?php
						break;

						case "checkbox":
							$picked = is_array($val) ? $val : array();
					?>
						<div class="apply-choice-list">
						<?php $ri = 0; foreach ($options as $optVal => $optLabel) { $ri++; ?>
							<label class="apply-choice" for="<?php echo $inputID . "_" . $ri; ?>">
								<input type="checkbox" id="<?php echo $inputID . "_" . $ri; ?>"
									name="<?php echo ApplyCore::esc($key); ?>[]"
									value="<?php echo ApplyCore::esc($optVal); ?>"<?php if (in_array($optLabel, $picked) || in_array($optVal, $picked)) echo ' checked'; ?>>
								<span><?php echo ApplyCore::esc($optLabel); ?></span>
							</label>
						<?php } ?>
						</div>
					<?php
						break;

						case "consent":
					?>
						<label class="apply-choice apply-consent" for="<?php echo $inputID; ?>">
							<input type="checkbox" id="<?php echo $inputID; ?>" name="<?php echo ApplyCore::esc($key); ?>"
								value="y"<?php if ($val == "y") echo ' checked'; ?><?php if ($req) echo ' required'; ?>>
							<span><?php echo ApplyCore::esc($field["fieldLabel"]); ?><?php if ($req) { ?><span class="apply-req">*</span><?php } ?></span>
						</label>
					<?php
						break;

						case "date":
					?>
						<input class="apply-input" type="date" id="<?php echo $inputID; ?>"
							name="<?php echo ApplyCore::esc($key); ?>"
							value="<?php echo ApplyCore::esc(is_array($val) ? "" : $val); ?>"<?php if ($req) echo ' required'; ?>>
					<?php
						break;

						default:
							$htmlType = "text";
							if ($type == "email")  { $htmlType = "email"; }
							if ($type == "tel")    { $htmlType = "tel"; }
							if ($type == "number") { $htmlType = "number"; }
					?>
						<input class="apply-input" type="<?php echo $htmlType; ?>" id="<?php echo $inputID; ?>"
							name="<?php echo ApplyCore::esc($key); ?>"
							value="<?php echo ApplyCore::esc(is_array($val) ? "" : $val); ?>"
							placeholder="<?php echo ApplyCore::esc($ph); ?>"
							autocomplete="<?php echo $htmlType == "email" ? "email" : ($htmlType == "tel" ? "tel" : "on"); ?>"<?php if ($req) echo ' required'; ?>>
					<?php
						break;
					}
					?>

					<?php if ($field["fieldHelp"] != "") { ?>
					<span class="apply-help"><?php echo ApplyCore::esc($field["fieldHelp"]); ?></span>
					<?php } ?>

					<?php if ($err != "") { ?>
					<span class="apply-error"><?php echo ApplyCore::esc($err); ?></span>
					<?php } ?>

				</div>
				<?php } ?>
				</div>

				<!-- robot хамгаалалт: жинхэнэ хэрэглэгч эдгээрийг хардаггүй -->
				<div class="apply-hp" aria-hidden="true">
					<label>Website<input type="text" name="applyWebsite" tabindex="-1" autocomplete="off" value=""></label>
				</div>
				<input type="hidden" name="applyTs" value="<?php echo time(); ?>">
				<input type="hidden" name="applyType" value="<?php echo ApplyCore::esc($applyType); ?>">
				<input type="hidden" name="frmApplyPost" value="1">

				<button type="submit" class="apply-btn"><?php echo ApplyCore::esc($applySet["submitLabel"]); ?></button>
			</form>

		<?php } ?>

		</div>
	</div>
</section>
