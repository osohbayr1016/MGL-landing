<script type='text/javascript' src='/assets/plugins/tinymce/tinymce.min.js'></script>
<script>
$(document).ready(function () {

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
			success: function (msg) { $("#orderModalFrm").html(msg); }
		});

		return false;
	});

	$(document).on("input change", ".regColorPick", function () {
		$("#" + $(this).attr("data-target")).val($(this).val());
	});

	$(document).on("click", ".regSubMoveBtn", function () {

		var btn = $(this);

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: {
				frmPost: "regSubMove",
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

	$(document).on("click", ".regSubDelBtn", function () {

		if (!confirm("Энэ мөрийг устгах уу?")) {
			return false;
		}

		var id = $(this).attr("data-id");

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: { frmPost: "regSubDel", frmDelID: id, ajaxOrder: 1 },
			dataType: "json",
			success: function () { $("#regSubRow" + id).fadeOut(200, function () { $(this).remove(); }); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

});
</script>
