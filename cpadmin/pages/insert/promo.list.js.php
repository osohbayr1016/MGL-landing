<script type='text/javascript' src='/assets/plugins/tinymce/tinymce.min.js'></script>
<style>
.js-order-input.order-saved { border-color: #1ab394; background: #f3fcf9; }
.js-order-input.order-busy { opacity: 0.6; }
</style>
<script>
$(document).ready(function() {
	
	
	$(document).on('click',".accessModBtn", function(){
		
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

	$(document).on('change', '.js-order-input', function(){
		var $inp = $(this);
		var postType = $inp.data('post');
		var newOrder = parseInt($inp.val(), 10);

		if(isNaN(newOrder) || newOrder < 1){
			newOrder = 1;
			$inp.val(newOrder);
		}

		var payload = {
			frmPost: postType,
			frmOrder: newOrder,
			ajaxOrder: 1
		};

		if(postType === 'ceoOrderSet'){
			payload.ceoID = $inp.data('ceo-id');
			payload.pageID = $inp.data('page-id') || 0;
		}

		if(postType === 'schOrderSet'){
			payload.schID = $inp.data('sch-id');
			payload.parentID = $inp.data('parent-id');
			payload.schKey = $inp.data('sch-key') || 0;
		}

		$inp.addClass('order-busy').prop('disabled', true);

		$.post('/userPost/insert', payload, function(res){
			$inp.removeClass('order-busy').prop('disabled', false);
			if(res && res.ok){
				$inp.addClass('order-saved');
				setTimeout(function(){ $inp.removeClass('order-saved'); }, 900);
			}
		}, 'json').fail(function(){
			$inp.removeClass('order-busy').prop('disabled', false);
			alert('Дэс дугаар хадгалахад алдаа гарлаа.');
		});
	});
	
	
});
</script>
