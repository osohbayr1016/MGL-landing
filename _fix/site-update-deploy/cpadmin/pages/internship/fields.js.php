<script>
$(document).ready(function () {

	$(document).on("click", ".applyModBtn", function () {

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

	$(document).on("click", ".applyFieldMoveBtn", function () {

		var btn = $(this);

		$.ajax({
			type: "POST",
			url: "/userPost/internship",
			data: {
				frmPost: "applyFieldMove",
				frmFieldID: btn.attr("data-id"),
				frmDir: btn.attr("data-dir"),
				ajaxOrder: 1
			},
			dataType: "json",
			success: function () { window.location.reload(); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

	$(document).on("click", ".applyFieldDelBtn", function () {

		if (!confirm("Энэ асуултыг устгах уу?\n\nӨмнө ирсэн хүсэлтүүдийн энэ талбарын хариулт Excel-д гарахаа болино.")) {
			return false;
		}

		var id = $(this).attr("data-id");

		$.ajax({
			type: "POST",
			url: "/userPost/internship",
			data: { frmPost: "applyFieldDel", frmDelID: id, ajaxOrder: 1 },
			dataType: "json",
			success: function () { $("#applyFieldRow" + id).fadeOut(200, function () { $(this).remove(); }); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

});
</script>
