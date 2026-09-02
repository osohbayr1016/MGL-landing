<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			<div class="modal-title"><?php echo RegistrationCore::esc($subTypeObj["subLabel"]);?></div>
		</div>

		<form id="regBlockFrm" action="/userPost/registration" method="post">
		<div class="modal-body">

			<div class="form-group">
				<label class="font-noraml">Дэс дугаар</label>
				<input type="number" min="1" class="form-control input-sm" name="frmOrder" value="<?php echo (int)$subOrder;?>">
			</div>

			<hr style="margin:10px 0 18px">

			<?php
			$frmCols   = $subTypeObj["subCols"];
			$frmData   = $subData;
			$frmIDPref = "rs";
			include $clkMenuModDir . "block.frm.inputs.php";
			?>

		</div>
		<div class="modal-footer">
			<input type="hidden" name="frmPost" value="regSubBlock">
			<input type="hidden" name="frmParentID" value="<?php echo (int)$subParentID;?>">
			<?php if(!$subIsNew){ ?>
			<input type="hidden" name="frmEditID" value="<?php echo (int)$editSubID;?>">
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
	if (window.tinymce) {
		tinymce.init({
			selector: "#orderModalFrm .regEditor",
			theme: "modern",
			height: 220,
			plugins: ["advlist autolink link image lists charmap hr anchor code fullscreen paste responsivefilemanager textcolor"],
			toolbar: "undo redo | bold italic | bullist numlist | link image responsivefilemanager | forecolor | code",
			external_filemanager_path: "/assets/plugins/filemanager/",
			external_plugins: { "filemanager": "/assets/plugins/filemanager/plugin.min.js" },
			setup: function (ed) {
				ed.on("change keyup", function () {
					$("#" + ed.getElement().getAttribute("data-target")).val(ed.getContent());
				});
			}
		});
	}

	$("#regBlockFrm").on("submit", function () {
		if (window.tinymce) {
			$("#orderModalFrm .regEditor").each(function () {
				var ed = tinymce.get($(this).attr("id"));
				var target = $(this).attr("data-target");
				if (ed && target) { $("#" + target).val(ed.getContent()); }
			});
		}
		return true;
	});
});
</script>
