<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title">Бүтээгдэхүүн устгах</div>
        </div>
        <form action="/userPost/insert" id="frmSch" method="post">
        <div class="modal-body">
            
            <?php 
			if($isDelete){
				?>
            Та <strong><?php echo $selNewObj["ceoName"]?></strong> нэртэй бүтээгдэхүүнг устгахдаа итгэлтэй байна уу
            <?php
			}
			else{
			?>
           <div class="alert alert-danger" role="alert"><strong><?php echo $selNewObj["newsTitle"]?></strong> мэдээг устгах боломжгүй!</div>
           <?php } ?>
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="ceoDel" />
			<input name="frmDelID" type="hidden" value="<?php echo $selNewsID;?>" />
                            
            <button type="button" class="btn btn-white" data-dismiss="modal">Болих</button>
            <?php if($isDelete){?>
            <button type="submit" class="btn btn-danger">Устгах</button>
            <?php }?>
        </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
	
	$('#frmSch').submit(function(event) {
		
		
		$.ajax({
			type: "POST",
			url: "/userPost/insert",
			data: $('#frmSch').serialize(),
			dataType: "html",
			success: function(data){
				
				$('#proTr<?php echo $selNewsID;?>').slideUp();			
				$('#orderModalFrm').modal('hide');
			}
			
		});	
		
		return false;
		
	});
	
});
</script>