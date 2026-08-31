<link href="/assets/plugins/dataTables/css/dataTables.bootstrap.css" rel="stylesheet">
<link href="/assets/plugins/dataTables/css/dataTables.responsive.css" rel="stylesheet">
<link href="/assets/plugins/dataTables/css/dataTables.tableTools.min.css" rel="stylesheet">
<script src="/assets/plugins/dataTables/js/jquery.dataTables.js"></script>
<script src="/assets/plugins/dataTables/js/dataTables.bootstrap.js"></script>
<script src="/assets/plugins/dataTables/js/dataTables.responsive.js"></script>
<script src="/assets/plugins/dataTables/js/dataTables.tableTools.min.js"></script>
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
	
	$('.dataTables-example').dataTable({
		responsive: true,
		"dom": 'T<"clear">lfrtip',
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 4 ] }
       ],
		"tableTools": {
			"sSwfPath": "/assets/plugins/dataTables/js/swf/copy_csv_xls_pdf.swf"
		}
	});
	
});
</script>