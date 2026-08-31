<div class="row">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="font-noraml">Гарчиг</label>
			<div >
				<input type="text" class="form-control input-sm" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["schTitle"];?>">
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label class="font-noraml">Дэлгэрэнгүй гарчиг</label><br />
			<div >
				<input type="text" class="form-control input-sm" name="frmSub" value="<?php echo $selTypeObj["schSub"];?>">
			</div>
		</div>
	</div>
</div>
<div class="form-group">
	<label class="font-noraml">Тайлбар</label><br />
	<div >
		<textarea  class="form-control input-sm" style="height:100px" id="frmNote" name="frmNote"><?php echo $selTypeObj["schNote"];?></textarea>
	</div>
</div>  
<div class="form-group" >
	<label class="font-noraml">Видео</label>
	<div class="input-group">
		<input type="text" name="frmPic" id="postVidLink" value="<?php echo $selTypeObj["schPic"];?>" class="form-control" placeholder="Зураг...">
		<span class="input-group-btn">
		<button class="btn btn-default" onclick="open_popup('/assets/plugins/filemanager/dialog.php?popup=1&field_id=postVidLink')" type="button">Зураг сонгох</button>
		</span>
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