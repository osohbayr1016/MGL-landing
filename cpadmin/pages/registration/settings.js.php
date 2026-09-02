<script>
function regOpenPicker(fieldID) {
	var w = 880, h = 570;
	var l = Math.floor((screen.width - w) / 2);
	var t = Math.floor((screen.height - h) / 2);
	window.open('/assets/plugins/filemanager/dialog.php?popup=1&field_id=' + fieldID,
		'ResponsiveFilemanager',
		'scrollbars=1,width=' + w + ',height=' + h + ',top=' + t + ',left=' + l);
}
</script>
