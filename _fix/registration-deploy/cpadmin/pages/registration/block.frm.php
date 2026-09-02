<div class="modal-dialog modal-lg">
	<div class="modal-content animated bounceInRight">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			<div class="modal-title">
				<i class="<?php echo RegistrationCore::esc($blockTypeObj["icon"]);?>"></i>
				<?php echo $blockIsNew ? "Блок нэмэх" : "Блок засах";?>
			</div>
		</div>

		<form id="regBlockFrm" action="/userPost/registration" method="post">
		<div class="modal-body">

			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
						<label class="font-noraml">Блокийн төрөл</label>
						<?php if($blockIsNew){ ?>
						<select class="form-control input-sm" id="regBlockTypeSel">
							<?php foreach($regBlockTypes as $tk=>$tobj){ ?>
							<option value="<?php echo RegistrationCore::esc($tk);?>"<?php if($tk==$blockType) echo ' selected';?>><?php echo RegistrationCore::esc($tobj["label"]);?></option>
							<?php } ?>
						</select>
						<?php } else { ?>
						<p class="form-control-static"><strong><?php echo RegistrationCore::esc($blockTypeObj["label"]);?></strong></p>
						<?php } ?>
						<small class="text-muted"><?php echo RegistrationCore::esc($blockTypeObj["desc"]);?></small>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label class="font-noraml">Дэс дугаар</label>
						<input type="number" min="1" class="form-control input-sm" name="frmOrder" value="<?php echo (int)$blockOrder;?>">
					</div>
				</div>
			</div>

			<hr style="margin:10px 0 18px">

			<?php
			$frmCols   = $blockTypeObj["cols"];
			$frmData   = $blockData;
			$frmIDPref = "rb";
			include $clkMenuModDir . "block.frm.inputs.php";
			?>

			<?php if(RegistrationCore::hasSub($blockType) && !$blockIsNew){ ?>
			<div class="alert alert-info" style="margin-bottom:0">
				<i class="fa fa-info-circle"></i>
				<?php echo RegistrationCore::esc($blockTypeObj["subLabel"]);?>-үүдийг
				<a href="/registration/subList/<?php echo (int)$editBlockID;?>">энэ холбоосоор</a> нэмж, засна.
			</div>
			<?php } elseif(RegistrationCore::hasSub($blockType)){ ?>
			<div class="alert alert-info" style="margin-bottom:0">
				<i class="fa fa-info-circle"></i>
				Эхлээд блокоо хадгална уу — дараа нь <?php echo RegistrationCore::esc($blockTypeObj["subLabel"]);?> нэмнэ.
			</div>
			<?php } ?>

		</div>
		<div class="modal-footer">
			<input type="hidden" name="frmPost" value="regBlock">
			<input type="hidden" name="frmBlockType" value="<?php echo RegistrationCore::esc($blockType);?>">
			<?php if(!$blockIsNew){ ?>
			<input type="hidden" name="frmEditID" value="<?php echo (int)$editBlockID;?>">
			<?php } ?>
			<button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
			<button type="submit" class="btn btn-primary">Хадгалах</button>
		</div>
		</form>
	</div>
</div>

<script>
function regOpenPicker(fieldID) {
	var w = 880, h = 570;
	var l = Math.floor((screen.width - w) / 2);
	var t = Math.floor((screen.height - h) / 2);
	window.open('/assets/plugins/filemanager/dialog.php?popup=1&field_id=' + fieldID,
		'ResponsiveFilemanager',
		'scrollbars=1,width=' + w + ',height=' + h + ',top=' + t + ',left=' + l);
}

$(function () {

	/* Төрөл солиход формыг дахин ачаална */
	$("#regBlockTypeSel").on("change", function () {
		var t = $(this).val();

		$("#orderModalFrm").html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');

		$.ajax({
			type: "POST",
			url: "/registration/blockEdit/0",
			data: { modAjax: "ok", type: t },
			dataType: "html",
			success: function (msg) { $("#orderModalFrm").html(msg); }
		});
	});

	/* Editor талбарууд */
	if (window.tinymce) {
		tinymce.init({
			selector: "#orderModalFrm .regEditor",
			theme: "modern",
			height: 240,
			plugins: ["advlist autolink link image lists charmap hr anchor code fullscreen media paste responsivefilemanager textcolor"],
			toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image responsivefilemanager | forecolor backcolor | code",
			image_advtab: true,
			external_filemanager_path: "/assets/plugins/filemanager/",
			filemanager_title: "Responsive Filemanager",
			external_plugins: { "filemanager": "/assets/plugins/filemanager/plugin.min.js" },
			setup: function (ed) {
				ed.on("change keyup", function () {
					$("#" + ed.getElement().getAttribute("data-target")).val(ed.getContent());
				});
			}
		});
	}

	$("#regBlockFrm").on("submit", function () {

		/* Editor-ийн агуулгыг нуугдсан талбар руу шилжүүлнэ */
		if (window.tinymce) {
			$("#orderModalFrm .regEditor").each(function () {
				var ed = tinymce.get($(this).attr("id"));
				var target = $(this).attr("data-target");
				if (ed && target) {
					$("#" + target).val(ed.getContent());
				}
			});
		}

		return true;
	});
});
</script>
