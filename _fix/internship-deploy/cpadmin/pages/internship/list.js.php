<script>
$(document).ready(function () {

	/* Дэлгэрэнгүй / формын modal */
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

	/* Хүсэлт устгах */
	$(document).on("click", ".applyDelBtn", function () {

		var btn = $(this);
		var id = btn.attr("data-id");

		if (!confirm(btn.attr("data-name") + " — энэ хүсэлтийг устгах уу?\n\nХавсаргасан файл нь бас устана.")) {
			return false;
		}

		$.ajax({
			type: "POST",
			url: "/userPost/internship",
			data: { frmPost: "applyEntryDel", frmDelID: id, ajaxOrder: 1 },
			dataType: "json",
			success: function () { $("#applyRow" + id).fadeOut(200, function () { $(this).remove(); }); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

	/* Дэлгэрэнгүй цонхон дээрх төлөв/тэмдэглэл хадгалах */
	$(document).on("submit", "#applyViewForm", function () {

		var frm = $(this);

		$.ajax({
			type: "POST",
			url: "/userPost/internship",
			data: frm.serialize() + "&ajaxOrder=1",
			dataType: "json",
			success: function () { window.location.reload(); },
			error: function () { window.location.reload(); }
		});

		return false;
	});

});
</script>
