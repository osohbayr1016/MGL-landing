<script src="/assets/js/jquery.fullscreen-0.4.1.min.js?v=3"></script>
<script>
$(document).ready(function () { 
	
	
	$("#slideFullScreenBtn").click(function() {
		$(this).parent().toggleClass("filter-close");	
		$(this).parent().toggleClass("filter-open");
		
	});

//	$('#support').text($.fullscreen.isNativelySupported() ? 'supports' : 'doesn\'t support');
	// open in fullscreen
	$('#slideFullScreenBtn').click(function() {
		$('#projectFullscreen').fullscreen();
		return false;
	});
	// exit fullscreen
	$('#fullscreen .exitfullscreen').click(function() {
		$.fullscreen.exit();
		return false;
	});
	// document's event
	$(document).bind('fscreenchange', function(e, state, elem) {
		// if we currently in fullscreen mode
		if ($.fullscreen.isFullScreen()) {
			
			$('#projectFullscreen').addClass("fullscreenSlide");
			$('#projectFullscreen .requestfullscreen').hide();
			$('#projectFullscreen .exitfullscreen').show();
		} else {
			$('#projectFullscreen').removeClass("fullscreenSlide");
			$('#projectFullscreen .requestfullscreen').show();
			$('#projectFullscreen .exitfullscreen').hide();
		}
	
	});
	
	
	
});
</script>