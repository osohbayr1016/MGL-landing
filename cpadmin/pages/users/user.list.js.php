<script>
$(document).ready(function() {
	
	$(".accessModBtn").click(function(){
		
		var linkURL = $(this).attr("href");
		
		
		$('#orderModalFrm').html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');
		$('#orderModalFrm').modal()        
		$('#orderModalFrm').modal({ keyboard: false })
		$('#orderModalFrm').modal('show')

		$.ajax({
			type: "POST",
			url: linkURL,
			data: '&modAjax=ok',
			dataType: "html",
			success: function(msg){
				
				if(parseInt(msg)!=0)
				{				
					
					$('#orderModalFrm').html(msg);			
		
				}
			}
			
		});	
		
		return false;
			
	});
	
	
	$(".orderPaymentBtn").click(function(){
		
		var orderID = $(this).attr("data-order-id");
		$('#orderModalFrm').html('<div class="modal-body text-center">Түр хүлээнэ үү ...</div>');
		$('#orderModalFrm').modal()        
		$('#orderModalFrm').modal({ keyboard: false })
		$('#orderModalFrm').modal('show')

		$.ajax({
			type: "POST",
			url: "/modu/modAjax",
			data: '&modName=orders&subPage=payModal&orderID='+orderID,
			dataType: "html",
			success: function(msg){
				
				if(parseInt(msg)!=0)
				{				
					
					$('#orderModalFrm').html(msg);			
		
				}
			}
			
		});	
			
	});
	
});
</script>