<?php
/**
 * Блокийн талбаруудыг каталогийн тодорхойлолтоос автоматаар зурна.
 *
 * Хүлээж авах хувьсагчид:
 *   $frmCols   — RegistrationCore::blockTypes()[type]["cols"] эсвэл ["subCols"]
 *   $frmData   — одоо хадгалагдсан утгууд (assoc)
 *   $frmIDPref — HTML id-ийн угтвар (нэг хуудсанд давхцахаас сэргийлнэ)
 *
 * Оролтын нэр нь бүгд frmVal[key] — post.sys.php үүнийг JSON болгож хадгална.
 */

if (!isset($frmIDPref)) {
	$frmIDPref = "rb";
}

foreach ($frmCols as $col) {

	$key   = $col["key"];
	$name  = "frmVal[" . $key . "]";
	$id    = $frmIDPref . preg_replace('/[^A-Za-z0-9_]/', "", $key);
	$val   = isset($frmData[$key]) ? $frmData[$key] : (isset($col["def"]) ? $col["def"] : "");
	$type  = $col["type"];
	$help  = isset($col["help"]) ? $col["help"] : "";
?>

	<div class="form-group">

		<?php if ($type != "bool") { ?>
		<label class="font-noraml"><?php echo RegistrationCore::esc($col["name"]); ?></label>
		<?php } ?>

		<?php
		switch ($type) {

			case "textarea":
		?>
			<textarea class="form-control input-sm" rows="3" name="<?php echo $name; ?>" id="<?php echo $id; ?>"><?php echo RegistrationCore::esc($val); ?></textarea>
		<?php
			break;

			case "code":
		?>
			<textarea class="form-control input-sm" rows="8" spellcheck="false" style="font-family:monospace;font-size:12px"
				name="<?php echo $name; ?>" id="<?php echo $id; ?>"><?php echo RegistrationCore::esc($val); ?></textarea>
		<?php
			break;

			case "editor":
		?>
			<textarea class="form-control input-sm regEditor" id="<?php echo $id; ?>_ed" data-target="<?php echo $id; ?>"><?php echo RegistrationCore::esc($val); ?></textarea>
			<textarea style="display:none" class="regEditorVal" id="<?php echo $id; ?>" name="<?php echo $name; ?>"><?php echo RegistrationCore::esc($val); ?></textarea>
		<?php
			break;

			case "file":
		?>
			<div class="input-group">
				<input type="text" class="form-control input-sm" name="<?php echo $name; ?>" id="<?php echo $id; ?>"
					value="<?php echo RegistrationCore::esc($val); ?>" placeholder="/postpic/image/...">
				<span class="input-group-btn">
					<button class="btn btn-default btn-sm" type="button"
						onclick="regOpenPicker('<?php echo $id; ?>')">Сонгох</button>
				</span>
			</div>
			<?php if ($val != "") { ?>
			<div style="margin-top:6px">
				<img src="<?php echo RegistrationCore::esc(newsPicFnc(0, $val)); ?>" alt="" style="max-height:70px;border:1px solid #e5e5e5">
			</div>
			<?php } ?>
		<?php
			break;

			case "number":
		?>
			<input type="number" class="form-control input-sm" name="<?php echo $name; ?>" id="<?php echo $id; ?>"
				value="<?php echo RegistrationCore::esc($val); ?>">
		<?php
			break;

			case "color":
		?>
			<div class="input-group">
				<input type="color" class="form-control input-sm regColorPick" style="padding:2px;width:46px"
					value="<?php echo $val != "" ? RegistrationCore::esc($val) : "#000000"; ?>" data-target="<?php echo $id; ?>">
				<input type="text" class="form-control input-sm" name="<?php echo $name; ?>" id="<?php echo $id; ?>"
					value="<?php echo RegistrationCore::esc($val); ?>" placeholder="хоосон = ил тод">
			</div>
		<?php
			break;

			case "select":
				$opts = RegistrationCore::parseOptions(isset($col["opt"]) ? $col["opt"] : "");
		?>
			<select class="form-control input-sm" name="<?php echo $name; ?>" id="<?php echo $id; ?>">
				<?php foreach ($opts as $ov => $ol) { ?>
				<option value="<?php echo RegistrationCore::esc($ov); ?>"<?php if ((string)$val === (string)$ov) echo ' selected'; ?>><?php echo RegistrationCore::esc($ol); ?></option>
				<?php } ?>
			</select>
		<?php
			break;

			case "bool":
		?>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="y"<?php if ($val == "y") echo ' checked'; ?>>
					<?php echo RegistrationCore::esc($col["name"]); ?>
				</label>
			</div>
		<?php
			break;

			default:
		?>
			<input type="text" class="form-control input-sm" autocomplete="off" name="<?php echo $name; ?>" id="<?php echo $id; ?>"
				value="<?php echo RegistrationCore::esc($val); ?>">
		<?php
			break;
		}
		?>

		<?php if ($help != "") { ?>
		<small class="text-muted"><?php echo RegistrationCore::esc($help); ?></small>
		<?php } ?>

	</div>

<?php } ?>
