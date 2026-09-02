<?php
/**
 * Бүртгэлийн форм.
 *
 * Талбарууд CP Admin -> "Талбар тохируулах" хэсгээс ирнэ ($regFields),
 * тул энд ямар ч талбар hardcode хийгээгүй.
 */

$frmMax   = (int)RegistrationCore::val($regData, "maxWidth", "620");
$frmLabel = RegistrationCore::val($regData, "labelShow", "y") == "y";
?>
<section id="registration-form" class="reg-section reg-form-section"
	style="<?php echo RegistrationCore::sectionStyle($regData, 80, 100); ?>">
	<div class="reg-wrap">
		<div class="reg-form-box" style="max-width:<?php echo $frmMax > 0 ? $frmMax : 620; ?>px">

			<?php if ($regDone) { ?>

			<div class="reg-state reg-state-ok">
				<i class="fa fa-check-circle"></i>
				<h2<?php echo RegistrationCore::editAttr($regEdit, "setting", 0, "successTitle"); ?>><?php echo RegistrationCore::esc($regSet["successTitle"]); ?></h2>
				<p<?php echo RegistrationCore::editAttr($regEdit, "setting", 0, "successText"); ?>><?php echo nl2br(RegistrationCore::esc($regSet["successText"])); ?></p>
			</div>

			<?php } elseif (!$regStatus["open"]) { ?>

			<div class="reg-state reg-state-<?php echo RegistrationCore::esc($regStatus["state"]); ?>">
				<i class="fa <?php echo $regStatus["state"] == "full" ? "fa-users" : "fa-lock"; ?>"></i>
				<h2><?php echo RegistrationCore::esc($regStatus["title"]); ?></h2>
				<p><?php echo nl2br(RegistrationCore::esc($regStatus["text"])); ?></p>
				<?php if ($regEdit) { ?><span class="reg-edit-hint">Энэ текстийг CP Admin -> Тохиргоо хэсгээс солино.</span><?php } ?>
			</div>

			<?php } else { ?>

			<?php if (RegistrationCore::val($regData, "title") != "" || $regEdit) { ?>
			<h2 class="reg-form-title"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "title"); ?>><?php echo RegistrationCore::esc(RegistrationCore::val($regData, "title")); ?></h2>
			<?php } ?>

			<?php if (RegistrationCore::val($regData, "subtitle") != "" || $regEdit) { ?>
			<p class="reg-form-sub"<?php echo RegistrationCore::editAttr($regEdit, "block", $regBlockID, "subtitle"); ?>><?php echo nl2br(RegistrationCore::esc(RegistrationCore::val($regData, "subtitle"))); ?></p>
			<?php } ?>

			<?php if (isset($regErrors["_form"])) { ?>
			<div class="reg-alert"><?php echo RegistrationCore::esc($regErrors["_form"]); ?></div>
			<?php } ?>

			<form class="reg-form" method="post" action="/registration#registration-form" novalidate>
				<div class="reg-form-grid">
				<?php
				foreach ($regFields as $field) {

					$key    = $field["fieldKey"];
					$type   = $field["fieldType"];
					$width  = $field["fieldWidth"] != "" ? $field["fieldWidth"] : "full";
					$req    = (int)$field["fieldRequired"] == 1;
					$err    = isset($regErrors[$key]) ? $regErrors[$key] : "";
					$val    = isset($regValues[$key]) ? $regValues[$key] : "";
					$ph     = $field["fieldPlaceholder"];
					$inputID = "regf_" . preg_replace('/[^A-Za-z0-9_]/', "", $key);
					$options = RegistrationCore::fieldHasOptions($type)
						? RegistrationCore::parseOptions($field["fieldOptions"])
						: array();
				?>
				<div class="reg-field reg-w-<?php echo RegistrationCore::esc($width); ?><?php if ($err != "") echo " reg-has-error"; ?>">

					<?php if ($frmLabel && $type != "consent") { ?>
					<label class="reg-label" for="<?php echo $inputID; ?>">
						<?php echo RegistrationCore::esc($field["fieldLabel"]); ?><?php if ($req) { ?><span class="reg-req">*</span><?php } ?>
					</label>
					<?php } ?>

					<?php
					switch ($type) {

						case "textarea":
					?>
						<textarea class="reg-input" id="<?php echo $inputID; ?>" name="<?php echo RegistrationCore::esc($key); ?>"
							rows="4" placeholder="<?php echo RegistrationCore::esc($ph); ?>"<?php if ($req) echo ' required'; ?>><?php echo RegistrationCore::esc(is_array($val) ? "" : $val); ?></textarea>
					<?php
						break;

						case "select":
					?>
						<select class="reg-input" id="<?php echo $inputID; ?>" name="<?php echo RegistrationCore::esc($key); ?>"<?php if ($req) echo ' required'; ?>>
							<option value=""><?php echo $ph != "" ? RegistrationCore::esc($ph) : "— сонгоно уу —"; ?></option>
							<?php foreach ($options as $optVal => $optLabel) { ?>
							<option value="<?php echo RegistrationCore::esc($optVal); ?>"<?php if ((string)$val === (string)$optVal) echo ' selected'; ?>><?php echo RegistrationCore::esc($optLabel); ?></option>
							<?php } ?>
						</select>
					<?php
						break;

						case "radio":
					?>
						<div class="reg-choice-list">
						<?php $ri = 0; foreach ($options as $optVal => $optLabel) { $ri++; ?>
							<label class="reg-choice" for="<?php echo $inputID . "_" . $ri; ?>">
								<input type="radio" id="<?php echo $inputID . "_" . $ri; ?>"
									name="<?php echo RegistrationCore::esc($key); ?>"
									value="<?php echo RegistrationCore::esc($optVal); ?>"<?php if ((string)$val === (string)$optVal) echo ' checked'; ?>>
								<span><?php echo RegistrationCore::esc($optLabel); ?></span>
							</label>
						<?php } ?>
						</div>
					<?php
						break;

						case "checkbox":
							$picked = is_array($val) ? $val : array();
					?>
						<div class="reg-choice-list">
						<?php $ri = 0; foreach ($options as $optVal => $optLabel) { $ri++; ?>
							<label class="reg-choice" for="<?php echo $inputID . "_" . $ri; ?>">
								<input type="checkbox" id="<?php echo $inputID . "_" . $ri; ?>"
									name="<?php echo RegistrationCore::esc($key); ?>[]"
									value="<?php echo RegistrationCore::esc($optVal); ?>"<?php if (in_array($optLabel, $picked) || in_array($optVal, $picked)) echo ' checked'; ?>>
								<span><?php echo RegistrationCore::esc($optLabel); ?></span>
							</label>
						<?php } ?>
						</div>
					<?php
						break;

						case "consent":
					?>
						<label class="reg-choice reg-consent" for="<?php echo $inputID; ?>">
							<input type="checkbox" id="<?php echo $inputID; ?>" name="<?php echo RegistrationCore::esc($key); ?>"
								value="y"<?php if ($val == "y") echo ' checked'; ?><?php if ($req) echo ' required'; ?>>
							<span><?php echo RegistrationCore::esc($field["fieldLabel"]); ?><?php if ($req) { ?><span class="reg-req">*</span><?php } ?></span>
						</label>
					<?php
						break;

						case "date":
					?>
						<input class="reg-input" type="date" id="<?php echo $inputID; ?>"
							name="<?php echo RegistrationCore::esc($key); ?>"
							value="<?php echo RegistrationCore::esc(is_array($val) ? "" : $val); ?>"<?php if ($req) echo ' required'; ?>>
					<?php
						break;

						default:
							$htmlType = "text";
							if ($type == "email")  { $htmlType = "email"; }
							if ($type == "tel")    { $htmlType = "tel"; }
							if ($type == "number") { $htmlType = "number"; }
					?>
						<input class="reg-input" type="<?php echo $htmlType; ?>" id="<?php echo $inputID; ?>"
							name="<?php echo RegistrationCore::esc($key); ?>"
							value="<?php echo RegistrationCore::esc(is_array($val) ? "" : $val); ?>"
							placeholder="<?php echo RegistrationCore::esc($ph); ?>"
							autocomplete="<?php echo $htmlType == "email" ? "email" : ($htmlType == "tel" ? "tel" : "on"); ?>"<?php if ($req) echo ' required'; ?>>
					<?php
						break;
					}
					?>

					<?php if ($field["fieldHelp"] != "") { ?>
					<span class="reg-help"><?php echo RegistrationCore::esc($field["fieldHelp"]); ?></span>
					<?php } ?>

					<?php if ($err != "") { ?>
					<span class="reg-error"><?php echo RegistrationCore::esc($err); ?></span>
					<?php } ?>

				</div>
				<?php } ?>
				</div>

				<!-- robot хамгаалалт: жинхэнэ хэрэглэгч эдгээрийг хардаггүй -->
				<div class="reg-hp" aria-hidden="true">
					<label>Website<input type="text" name="regWebsite" tabindex="-1" autocomplete="off" value=""></label>
				</div>
				<input type="hidden" name="regTs" value="<?php echo time(); ?>">
				<input type="hidden" name="frmRegPost" value="1">

				<button type="submit" class="reg-btn reg-submit"<?php echo RegistrationCore::editAttr($regEdit, "setting", 0, "submitLabel"); ?>><?php echo RegistrationCore::esc($regSet["submitLabel"]); ?></button>
			</form>

			<?php } ?>

		</div>
	</div>
</section>
