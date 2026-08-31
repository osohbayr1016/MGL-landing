<script type='text/javascript' src='/assets/plugins/tinymce/tinymce.min.js'></script>
<script src="/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
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

	if($("#ceoOrderTable tbody").length){
		$("#ceoOrderTable tbody").sortable({
			handle: ".drag-handle",
			helper: function(e, tr){
				var $originals = tr.children();
				var $helper = tr.clone();
				$helper.children().each(function(index){
					$(this).width($originals.eq(index).width());
				});
				return $helper;
			},
			update: function(){
				var ids = [];
				$("#ceoOrderTable tbody tr").each(function(){
					ids.push($(this).attr("data-ceo-id"));
				});
				$.post("/userPost/insert", { frmPost: "ceoReorder", ceoIDs: ids });
				$("#ceoOrderTable tbody tr").each(function(i){
					$(this).find(".order-num").text(i + 1);
				});
			}
		});
	}
	
	
});
</script>
