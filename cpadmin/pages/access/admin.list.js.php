<script src="/assets/plugins/chosen/js/chosen.jquery.js"></script>
<script>
$(document).ready(function() {
	
	
	$(".accessModBtn").click(function(){
		
		var linkURL = $(this).attr("href");
		
		
		$('#accessModalFrm').html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');
		$('#accessModalFrm').modal()        
		$('#accessModalFrm').modal({ keyboard: false })
		$('#accessModalFrm').modal('show')

		$.ajax({
			type: "POST",
			url: linkURL,
			data: '&modAjax=ok',
			dataType: "html",
			success: function(msg){
				
				if(parseInt(msg)!=0)
				{				
					
					$('#accessModalFrm').html(msg);			
		
				}
			}
			
		});	
		
		return false;
			
	});
	
});
</script>