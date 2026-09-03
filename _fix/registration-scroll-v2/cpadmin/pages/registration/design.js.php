<script type='text/javascript' src='/assets/plugins/tinymce/tinymce.min.js'></script>
<script>
/* Файл сонгогч (responsive filemanager) — хуудасны дэвсгэрт ашиглана */
function regOpenPicker(fieldID) {
	var w = 880, h = 570;
	var l = Math.floor((screen.width - w) / 2);
	var t = Math.floor((screen.height - h) / 2);
	window.open('/assets/plugins/filemanager/dialog.php?popup=1&field_id=' + fieldID,
		'ResponsiveFilemanager',
		'scrollbars=1,width=' + w + ',height=' + h + ',top=' + t + ',left=' + l);
}

$(document).ready(function () {

	/* Модал форм нээх (блок нэмэх / засах) */
	$(document).on("click", ".regModBtn", function () {

		var linkURL = $(this).attr("href");

		$("#orderModalFrm").html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');
		$("#orderModalFrm").modal({ keyboard: false });
		$("#orderModalFrm").modal("show");

		$.ajax({
			type: "POST",
			url: linkURL,
			data: "&modAjax=ok",
			dataType: "html",
			success: function (msg) {
				$("#orderModalFrm").html(msg);
			}
		});

		return false;
	});

	/* Өнгө сонгогч -> текст талбар */
	$(document).on("input change", ".regColorPick", function () {
		$("#" + $(this).attr("data-target")).val($(this).val());
	});

	/* Блок дээш/доош */
	$(document).on("click", ".regMoveBtn", function () {

		var btn = $(this);

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: {
				frmPost: "regBlockMove",
				frmBlockID: btn.attr("data-id"),
				frmDir: btn.attr("data-dir"),
				ajaxOrder: 1
			},
			dataType: "json",
			success: function () { window.location.reload(); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

	/* Блок асаах / унтраах */
	$(document).on("click", ".regToggleBtn", function () {

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: {
				frmPost: "regBlockToggle",
				frmBlockID: $(this).attr("data-id"),
				ajaxOrder: 1
			},
			dataType: "json",
			success: function () { window.location.reload(); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

	/* Блок устгах */
	$(document).on("click", ".regBlockDelBtn", function () {

		if (!confirm("Энэ блокийг устгах уу? Дэд мөрүүд нь хамт устана.")) {
			return false;
		}

		var id = $(this).attr("data-id");

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: { frmPost: "regBlockDel", frmDelID: id, ajaxOrder: 1 },
			dataType: "json",
			success: function () { $("#regBlockRow" + id).fadeOut(200, function () { $(this).remove(); }); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

});
</script>
