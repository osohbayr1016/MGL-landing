<script>
$().ready(function(){
	
	
	
	alert('d');
	$('#frmMainReg').submit(function(event) {
		
		blurIS = 'ok';
		postFrm = false;
		
		alert('d');
		

		if($(".frmReq").length>0)
		$(".frmReq").each(function() {
			if($(this).find(".form-control").val()=='' || $(this).find(".form-control").val()== null){
				
				if(blurIS=='ok'){
					$(this).find(".form-control").focus();
					blurIS = '';
				}
				
				$(this).addClass("errDiv");
				$(this).removeClass("doneDiv");
				
				postFrm = false;
					
			}
			
		});
		
		if(postFrm){
			
			return true;
			
		}
		
		return false;
		
	});
	
});

</script>
