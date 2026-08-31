<div class="form-group">
	<label class="font-noraml">Гарчиг</label>
	<div >
		<input type="text" class="form-control input-sm" autocomplete="off" name="frmName" value="<?php echo $selTypeObj["schTitle"];?>">
	</div>
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="font-noraml">Үзүүлэлт</label>
			<div >
				<input type="text" class="form-control input-sm" autocomplete="off" name="frmPic" value="<?php echo $selTypeObj["frmPic"];?>">
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label class="font-noraml">Үзүүлэлт тайлбар</label>
			<div >
				<input type="text" class="form-control input-sm" autocomplete="off" name="frmSub" value="<?php echo $selTypeObj["schSub"];?>">
			</div>
		</div>
	</div>						
</div>
<div class="form-group">
	<label class="font-noraml">Бүртгүүлэх линк</label>
	<div >
		<input type="text" class="form-control input-sm" autocomplete="off" name="frmLink" value="<?php echo $selTypeObj["schLink"];?>">
	</div>
</div>
<div class="form-group">
	<label class="font-noraml">Тайлбар</label><br />
	<div >
		<textarea id="frmNote" class="form-control input-sm" name="frmNote"><?php echo $selTypeObj["schNote"];?></textarea>
		
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