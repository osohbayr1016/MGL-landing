           
            <div class="form-group">
                <label class="font-noraml">Гарчиг</label>
                <div >
                    <input type="text" class="form-control input-sm" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["schTitle"];?>">
                </div>
            </div>
			<div class="form-group">
				<label class="font-noraml">Дэлгэрэнгүй гарчиг</label><br />
				<div >
					<input type="text" class="form-control input-sm" name="frmSub" value="<?php echo $selTypeObj["schSub"];?>">
				</div>
            </div>
		   <div class="form-group">
				<label class="font-noraml">Тайлбар</label><br />
				<div >
                    <textarea  class="form-control input-sm" style="height:100px" id="frmNote" name="frmNote"><?php echo $selTypeObj["schNote"];?></textarea>
                    
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
				
				$('#courseListID').html(data);			
				$('#orderModalFrm').modal('hide');
			}
			
		});	
		
		return false;
		
	});
	
	  
});

</script>