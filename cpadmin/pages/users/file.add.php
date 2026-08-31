<div class="modal-dialog modal-md">
	<div class="modal-content animated bounceInRight">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="modal-title"><?php echo $selUserObj["companyName"];?> - ажлын файл</div>
        </div>
        <form action="/userPost/users" id="frmMainReg" enctype="multipart/form-data" method="post">
        <div class="modal-body">
            
            <div class="form-group frmReq">
                <label class="font-noraml">Файлын нэр</label>
                <div >
                    <input type="text" class="form-control input-sm" name="frmName" value="<?php echo $editFileObj["fileName"];?>">
                </div>
                <div class="frmErr">Файлын нэр оруулна уу</div>
            </div>
            <div class="form-group frmReq">
                <label class="font-noraml">Хэмжээ</label>
                <div >
                    <input type="text" class="form-control input-sm" name="frmSize" value="<?php echo $editFileObj["fileSize"];?>">
                </div>
                <div class="frmErr">Татах линк оруулна уу</div>
            </div>
            <div class="form-group frmReq">
                <label class="font-noraml">Татах линк</label>
                <div >
                    <input type="text" class="form-control input-sm" name="frmLink" value="<?php echo $editFileObj["fileLink"];?>">
                </div>
                <div class="frmErr">Татах линк оруулна уу</div>
            </div>    
            <div class="form-group frmReq">
                <label class="font-noraml">Татах нууц үг</label>
                <div >
                    <input type="text" class="form-control input-sm" name="frmPass" value="<?php echo $editFileObj["filepass"];?>">
                </div>
                <div class="frmErr">Татах нууц үг линк оруулна уу</div>
            </div>        
           
        </div>
        <div class="modal-footer">
            <input type="hidden" name="frmPost" value="filePost" />
            <?php 
			if($editID>0){
			?>
			<input name="frmEditID" id="frmEditID" type="hidden" value="<?php echo $editID;?>" />
			<?php } ?>
            <input name="frmUserID" type="hidden" value="<?php echo $userID;?>" />
            <button type="button" class="btn btn-white" data-dismiss="modal">Хаах</button>
            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </div>
        </form>
    </div>
</div>
<script>
$().ready(function(){
	
	
	
	$('#frmMainReg').submit(function(event) {
		
		blurIS = 'ok';
		var postFrm = true;
		

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
		
       
		
        return postFrm;
		
	});
	
});

</script>
