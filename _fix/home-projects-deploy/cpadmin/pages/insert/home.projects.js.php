<script src="/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
var hpCatalog = <?php echo $hpCatalogJson; ?>;
var hpById = {};
var hpPendingPics = {};
var hpActiveSlot = 0;
var hpDragging = false;
var hpSchId = <?php echo (int)$hpSchId; ?>;
for (var i = 0; i < hpCatalog.length; i++) {
	hpById[hpCatalog[i].id] = hpCatalog[i];
}

function hpOpenPopup(url) {
	var w = 880, h = 570;
	var l = Math.floor((screen.width - w) / 2);
	var t = Math.floor((screen.height - h) / 2);
	window.open(url, "ResponsiveFilemanager", "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
}

function hpRenderSlot($slot, p) {
	$slot.attr("data-ceo-id", p ? p.id : 0);
	$slot.find(".hp-img").attr("src", p ? p.pic : "");
	$slot.find(".hp-name").text(p ? p.name : "Төсөл сонгох");
	$slot.find(".hp-brand").text(p ? p.brand : "");
	$slot.toggleClass("hp-empty", !p);
}

function hpRenumber() {
	$("#hpGrid .hp-slot").not(".hp-placeholder").each(function (i) {
		var n = ("0" + (i + 1)).slice(-2);
		$(this).find(".hp-index").text(n);
	});
}

function hpOpenPicker(index) {
	hpActiveSlot = index;
	var $slots = $("#hpGrid .hp-slot").not(".hp-placeholder");
	var id = parseInt($slots.eq(index).attr("data-ceo-id"), 10) || 0;
	$("#hpPickerSearch").val("");
	$("#hpPicker .hp-pick").show().removeClass("is-current");
	$("#hpPicker .hp-pick[data-ceo-id='" + id + "']").addClass("is-current");
	$("#hpPicker").modal("show");
}

function hpAssign(id) {
	id = parseInt(id, 10);
	var $slots = $("#hpGrid .hp-slot").not(".hp-placeholder");
	var $target = $slots.eq(hpActiveSlot);
	var oldId = parseInt($target.attr("data-ceo-id"), 10) || 0;
	var $existing = $slots.filter("[data-ceo-id='" + id + "']");
	if ($existing.length && !$existing.is($target)) {
		hpRenderSlot($existing, hpById[oldId] || null);
	}
	hpRenderSlot($target, hpById[id] || null);
	$("#hpPicker").modal("hide");
}

window.responsive_filemanager_callback = function (fieldId) {
	var url = $("#" + fieldId).val();
	var $slot = $("#hpGrid .hp-slot").not(".hp-placeholder").eq(hpActiveSlot);
	var id = parseInt($slot.attr("data-ceo-id"), 10);
	if (!id || !url) {
		return;
	}
	hpPendingPics[id] = url;
	if (hpById[id]) {
		hpById[id].pic = url;
	}
	$slot.find(".hp-img").attr("src", url);
	$("#hpPicker .hp-pick[data-ceo-id='" + id + "'] img").attr("src", url);
};

$(function () {
	$("#hpGrid").sortable({
		items: ".hp-slot:not(.hp-placeholder)",
		placeholder: "hp-slot hp-placeholder",
		tolerance: "pointer",
		distance: 8,
		forcePlaceholderSize: true,
		start: function () { hpDragging = true; },
		stop: function () { hpRenumber(); setTimeout(function () { hpDragging = false; }, 40); }
	});
	$("#hpGrid").on("click", ".hp-slot:not(.hp-placeholder)", function () {
		if (hpDragging) {
			return;
		}
		hpOpenPicker($("#hpGrid .hp-slot").not(".hp-placeholder").index(this));
	});
	$("#hpPicker").on("click", ".hp-pick", function () {
		hpAssign($(this).data("ceo-id"));
	});
	$("#hpPickerSearch").on("keyup", function () {
		var q = ($(this).val() || "").toLowerCase();
		$("#hpPicker .hp-pick").each(function () {
			var n = (($(this).data("name") || "") + "").toLowerCase();
			$(this).toggle(n.indexOf(q) !== -1);
		});
	});
	$("#hpChangePic").on("click", function (e) {
		e.preventDefault();
		var id = parseInt($("#hpGrid .hp-slot").not(".hp-placeholder").eq(hpActiveSlot).attr("data-ceo-id"), 10) || 0;
		if (!id) {
			alert("Эхлээд төсөл сонгоно уу.");
			return;
		}
		hpOpenPopup("/assets/plugins/filemanager/dialog.php?popup=1&field_id=hpPicField&type=1");
	});
	$("#hpPicField").on("change", function () {
		window.responsive_filemanager_callback("hpPicField");
	});
	$("#hpSave").on("click", function () {
		var items = [];
		$("#hpGrid .hp-slot").not(".hp-placeholder").each(function () {
			items.push(parseInt($(this).attr("data-ceo-id"), 10) || 0);
		});
		var $btn = $(this).prop("disabled", true);
		$.post("/userPost/insert", {
			frmPost: "homeProjectsSave",
			schID: hpSchId,
			items: items,
			pics: hpPendingPics
		}, function (res) {
			$btn.prop("disabled", false);
			if (res && res.ok) {
				hpPendingPics = {};
				alert("Хадгаллаа.");
			} else {
				alert("Алдаа гарлаа.");
			}
		}, "json").fail(function () {
			$btn.prop("disabled", false);
			alert("Алдаа гарлаа.");
		});
	});
});
</script>
