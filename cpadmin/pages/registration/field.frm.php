<?php $isCoreField = $fieldObj["fieldCore"] != ""; ?>
<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			<div class="modal-title"><?php echo $fieldIsNew ? "Талбар нэмэх" : "Талбар засах";?></div>
		</div>

		<form action="/userPost/registration" method="post">
		<div class="modal-body">

			<div class="row">
				<div class="col-sm-7">
					<div class="form-group">
						<label class="font-noraml">Талбарын нэр (хэрэглэгчид харагдана)</label>
						<input type="text" class="form-control input-sm" name="frmLabel" required
							value="<?php echo RegistrationCore::esc($fieldObj["fieldLabel"]);?>">
					</div>
				</div>
				<div class="col-sm-5">
					<div class="form-group">
						<label class="font-noraml">Дэс дугаар</label>
						<input type="number" min="1" class="form-control input-sm" name="frmOrder"
							value="<?php echo (int)$fieldObj["fieldOrder"];?>">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="font-noraml">Түлхүүр үг (латинаар)</label>
						<?php if($isCoreField){ ?>
						<input type="text" class="form-control input-sm" value="<?php echo RegistrationCore::esc($fieldObj["fieldKey"]);?>" readonly>
						<small class="text-muted">Үндсэн талбарын түлхүүр өөрчлөгдөхгүй.</small>
						<?php } else { ?>
						<input type="text" class="form-control input-sm" name="frmKey"
							value="<?php echo RegistrationCore::esc($fieldObj["fieldKey"]);?>" placeholder="ж: company">
						<small class="text-muted">Хоосон бол нэрнээс автоматаар үүснэ.</small>
						<?php } ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="font-noraml">Төрөл</label>
						<?php if($isCoreField){ ?>
						<input type="text" class="form-control input-sm" readonly
							value="<?php echo RegistrationCore::esc($regFieldTypes[$fieldObj["fieldType"]]);?>">
						<?php } else { ?>
						<select class="form-control input-sm" name="frmType" id="regFieldType">
							<?php foreach($regFieldTypes as $tk=>$tl){ ?>
							<option value="<?php echo $tk;?>"<?php if($fieldObj["fieldType"]==$tk) echo ' selected';?>><?php echo $tl;?></option>
							<?php } ?>
						</select>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="form-group" id="regOptWrap" style="<?php echo RegistrationCore::fieldHasOptions($fieldObj["fieldType"]) ? "" : "display:none";?>">
				<label class="font-noraml">Сонголтууд</label>
				<textarea class="form-control input-sm" name="frmOptions" rows="4"
					placeholder="Мөр тус бүрд нэг сонголт бичнэ.&#10;Жишээ:&#10;Тийм&#10;Үгүй"><?php echo RegistrationCore::esc($fieldObj["fieldOptions"]);?></textarea>
			</div>

			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="font-noraml">Placeholder</label>
						<input type="text" class="form-control input-sm" name="frmPlaceholder"
							value="<?php echo RegistrationCore::esc($fieldObj["fieldPlaceholder"]);?>">
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="font-noraml">Туслах тайлбар</label>
						<input type="text" class="form-control input-sm" name="frmHelp"
							value="<?php echo RegistrationCore::esc($fieldObj["fieldHelp"]);?>">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<label class="font-noraml">Өргөн</label>
						<select class="form-control input-sm" name="frmWidth">
							<option value="full"<?php if($fieldObj["fieldWidth"]=="full") echo ' selected';?>>Бүтэн мөр</option>
							<option value="half"<?php if($fieldObj["fieldWidth"]=="half") echo ' selected';?>>Хагас мөр</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label class="font-noraml">Заавал бөглөх</label>
						<select class="form-control input-sm" name="frmRequired">
							<option value="1"<?php if((int)$fieldObj["fieldRequired"]==1) echo ' selected';?>>Тийм</option>
							<option value="0"<?php if((int)$fieldObj["fieldRequired"]!=1) echo ' selected';?>>Үгүй</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label class="font-noraml">Төлөв</label>
						<select class="form-control input-sm" name="frmStatus">
							<option value="1"<?php if((int)$fieldObj["fieldStatus"]==1) echo ' selected';?>>Идэвхтэй</option>
							<option value="0"<?php if((int)$fieldObj["fieldStatus"]!=1) echo ' selected';?>>Унтраасан</option>
						</select>
					</div>
				</div>
			</div>

		</div>
		<div class="modal-footer">
			<input type="hidden" name="frmPost" value="regField">
			<?php if(!$fieldIsNew){ ?>
			<input type="hidden" name="frmEditID" value="<?php echo (int)$editFieldID;?>">
			<?php } ?>
			<button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
			<button type="submit" class="btn btn-primary">Хадгалах</button>
		</div>
		</form>
	</div>
</div>

<script>
$(function () {
	$("#regFieldType").on("change", function () {
		var t = $(this).val();
		if (t === "select" || t === "radio" || t === "checkbox") {
			$("#regOptWrap").show();
		} else {
			$("#regOptWrap").hide();
		}
	});
});
</script>
