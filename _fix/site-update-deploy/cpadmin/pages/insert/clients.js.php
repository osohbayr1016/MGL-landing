<script src="/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
var clItems  = <?php echo json_encode($clItems, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var clSelId  = <?php echo (int)$clSelID; ?>;
var clUpRow  = null;

function clRow(item) {
	var $row = $(
		'<div class="cl-row">' +
			'<div class="cl-handle"><i class="fa fa-arrows"></i></div>' +
			'<div class="cl-logo">' +
				'<div class="cl-logo-box"><img alt=""></div>' +
				'<div class="cl-logo-btns">' +
					'<button type="button" class="btn btn-white cl-upload">Байршуулах</button>' +
				'</div>' +
			'</div>' +
			'<div class="cl-fields">' +
				'<div class="form-group"><label>Байгууллагын нэр</label>' +
					'<input type="text" class="form-control input-sm cl-name" placeholder="Жишээ: Тavan Bogd"></div>' +
				'<div class="form-group"><label>Вэб сайт (лого дээр дарахад нээгдэнэ)</label>' +
					'<input type="text" class="form-control input-sm cl-link" placeholder="tavanbogd.com"></div>' +
				'<div class="form-group"><label>Зургийн хаяг</label>' +
					'<input type="text" class="form-control input-sm cl-pic" placeholder="/postpic/image/clients/logo.png"></div>' +
			'</div>' +
			'<div class="cl-side">' +
				'<a href="#" class="btn btn-white btn-xs cl-open" target="_blank" rel="noopener">Сайт үзэх</a><br>' +
				'<button type="button" class="btn btn-danger btn-xs cl-del">Устгах</button>' +
			'</div>' +
		'</div>'
	);

	$row.data("id", item.id ? parseInt(item.id, 10) : 0);
	$row.find(".cl-name").val(item.title || "");
	$row.find(".cl-link").val(item.link || "");
	$row.find(".cl-pic").val(item.pic || "");
	clRowPic($row, item.picUrl || item.pic || "");
	clRowLink($row);

	return $row;
}

function clRowPic($row, url) {
	var $box = $row.find(".cl-logo-box");
	$box.toggleClass("cl-none", !url);
	$box.find("img").attr("src", url || "").toggle(!!url);
}

function clRowLink($row) {
	var link = $.trim($row.find(".cl-link").val());
	var href = link;

	if (href && !/^(https?:|mailto:|tel:|\/|#)/i.test(href)) {
		href = "https://" + href;
	}

	$row.find(".cl-open").attr("href", href || "#").toggleClass("disabled", !href);
}

function clRender() {
	var $list = $("#clList").empty();

	if (!clItems.length) {
		$list.append('<div class="cl-empty">Одоогоор харилцагч алга. "Харилцагч нэмэх" товчоор нэмнэ үү.</div>');
		return;
	}

	for (var i = 0; i < clItems.length; i++) {
		$list.append(clRow(clItems[i]));
	}
}

function clCollect() {
	var out = [];

	$("#clList .cl-row").each(function () {
		var $row = $(this);
		var pic = $.trim($row.find(".cl-pic").val());
		var name = $.trim($row.find(".cl-name").val());

		/* Хоосон мөрийг алгасна — лого ч, нэр ч байхгүй бол хадгалах зүйлгүй. */
		if (pic === "" && name === "") {
			return;
		}

		out.push({
			id: $row.data("id") || 0,
			title: name,
			link: $.trim($row.find(".cl-link").val()),
			pic: pic
		});
	});

	return out;
}

$(function () {
	clRender();

	$("#clSection").on("change", function () {
		window.location.href = "/insert/clients/" + parseInt($(this).val(), 10);
	});

	$("#clList").sortable({
		items: ".cl-row",
		handle: ".cl-handle",
		placeholder: "cl-placeholder",
		tolerance: "pointer",
		forcePlaceholderSize: true
	});

	$("#clAdd").on("click", function () {
		var $row = clRow({ id: 0 });
		$("#clList .cl-empty").remove();
		$("#clList").append($row);
		$row.find(".cl-name").focus();
	});

	$("#clList").on("click", ".cl-del", function () {
		var $row = $(this).closest(".cl-row");
		if ($row.data("id") > 0 && !confirm("Энэ харилцагчийг устгах уу?")) {
			return;
		}
		$row.remove();
		if (!$("#clList .cl-row").length) {
			$("#clList").append('<div class="cl-empty">Одоогоор харилцагч алга. "Харилцагч нэмэх" товчоор нэмнэ үү.</div>');
		}
	});

	$("#clList").on("input change", ".cl-link", function () {
		clRowLink($(this).closest(".cl-row"));
	});

	$("#clList").on("change", ".cl-pic", function () {
		var $row = $(this).closest(".cl-row");
		clRowPic($row, $.trim($(this).val()));
	});

	$("#clList").on("click", ".cl-upload", function () {
		clUpRow = $(this).closest(".cl-row");
		$("#clFile").val("").trigger("click");
	});

	$("#clFile").on("change", function () {
		if (!this.files || !this.files.length || !clUpRow) {
			return;
		}

		var $row = clUpRow;
		var data = new FormData();
		data.append("frmPost", "clientUpload");
		data.append("ajaxOrder", "1");
		data.append("frmLogo", this.files[0]);

		$row.addClass("cl-busy");

		$.ajax({
			url: "/userPost/insert",
			type: "POST",
			data: data,
			processData: false,
			contentType: false,
			dataType: "json"
		}).done(function (res) {
			if (res && res.ok && res.pic) {
				$row.find(".cl-pic").val(res.pic);
				clRowPic($row, res.url || res.pic);
			} else {
				alert((res && res.error) ? res.error : "Зураг байршуулж чадсангүй.");
			}
		}).fail(function () {
			alert("Зураг байршуулж чадсангүй.");
		}).always(function () {
			$row.removeClass("cl-busy");
		});
	});

	$("#clSave").on("click", function () {
		var $btn = $(this).prop("disabled", true).text("Хадгалж байна...");

		$.ajax({
			url: "/userPost/insert",
			type: "POST",
			dataType: "json",
			data: {
				frmPost: "clientsSave",
				ajaxOrder: "1",
				schID: clSelId,
				frmTitle: $("#clTitle").val(),
				frmHover: $("#clHover").is(":checked") ? "1" : "0",
				frmNewTab: $("#clNewTab").is(":checked") ? "1" : "0",
				items: JSON.stringify(clCollect())
			}
		}).done(function (res) {
			if (res && res.ok) {
				window.location.href = "/insert/clients/" + clSelId;
			} else {
				alert((res && res.error) ? res.error : "Хадгалж чадсангүй.");
				$btn.prop("disabled", false).text("Хадгалах");
			}
		}).fail(function () {
			alert("Хадгалж чадсангүй.");
			$btn.prop("disabled", false).text("Хадгалах");
		});
	});
});
</script>
