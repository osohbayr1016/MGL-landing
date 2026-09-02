<script>
$(document).ready(function () {

	/* Хуудасны хаягийг хуулах */
	$("#regCopyLink").on("click", function () {
		var inp = document.getElementById("regPageLink");
		inp.select();
		inp.setSelectionRange(0, 99999);

		try {
			document.execCommand("copy");
			$(this).text("Хууллаа");
			var btn = $(this);
			setTimeout(function () { btn.text("Хуулах"); }, 1600);
		} catch (e) {
			window.prompt("Хаягийг хуулна уу:", inp.value);
		}
	});

	/* Бүртгэл устгах */
	$(".regDelBtn").on("click", function () {

		var id = $(this).attr("data-id");
		var name = $(this).attr("data-name");

		if (!confirm('"' + name + '" гэсэн бүртгэлийг устгах уу?')) {
			return false;
		}

		$.ajax({
			type: "POST",
			url: "/userPost/registration",
			data: { frmPost: "regEntryDel", frmDelID: id },
			dataType: "html",
			success: function () {
				$("#regRow" + id).fadeOut(200, function () { $(this).remove(); });
			}
		});

		return false;
	});

});
</script>
