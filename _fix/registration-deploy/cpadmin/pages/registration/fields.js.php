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

	$(document).on("click", ".regFieldMoveBtn", function () {

		var btn = $(this);

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: {
				frmPost: "regFieldMove",
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

	$(document).on("click", ".regFieldDelBtn", function () {

		if (!confirm("Энэ талбарыг устгах уу?\n\nӨмнө нь бүртгүүлсэн хүмүүсийн энэ талбарын мэдээлэл Excel-д гарахаа болино.")) {
			return false;
		}

		var id = $(this).attr("data-id");

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: { frmPost: "regFieldDel", frmDelID: id, ajaxOrder: 1 },
			dataType: "json",
			success: function () { $("#regFieldRow" + id).fadeOut(200, function () { $(this).remove(); }); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

});
</script>
